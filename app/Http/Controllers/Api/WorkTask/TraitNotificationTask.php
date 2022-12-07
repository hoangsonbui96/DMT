<?php

namespace App\Http\Controllers\Api\WorkTask;

use App\ErrorReview;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\NotificationController;
use App\Jobs\SendEmail;
use App\model\PushToken;
use App\Project;
use App\Room;
use App\User;
use Carbon\Carbon;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;


trait TraitNotificationTask
{
    // Function send email && notification to mobile
    public function send($from_user, $member_id, $project_id, $contents = [], $is_send_email = true, $is_send_noti = true): bool
    {
        $project = Project::query()->where("id", $project_id)->where('Active', 1)->first();
        if (!$project) {
            return false;
        }
        $list_leader_id = explode(",", $project->Leader);
        array_pop($list_leader_id);
        array_shift($list_leader_id);

        $list_member_id = explode(",", $project->Member);
        array_pop($list_member_id);
        array_shift($list_member_id);

        $list_leader = array_map(function ($id) {
            return User::findOrFail($id);
        }, $list_leader_id);

        if (!$from_user->email && !$from_user->email_user) {
            $is_send_email = false;
        }
        foreach ($list_leader as $leader) {
            if ($leader->email || $leader->email_user)
                break;
            else
                $is_send_email = false;
        }

        $arr_token = PushToken::query()->where(function ($query) use ($from_user, $list_leader_id, $member_id) {
            $query->where('UserID', $from_user->id)
                ->orWhereIn('UserID', $list_leader_id)
                ->orWhere('UserID', $member_id);
        })
            ->where('allow_push', 1)
            ->whereNull('deleted_at')
            ->pluck('token_push')->toArray();

        $is_send_noti = count($arr_token) != 0;

        // Can send notification
        if ($is_send_noti) {
            $send_data = [];
            $send_data['id'] = 12;
            $send_data['data'] = 'TWK';
            $this->_sendNotificationTemplateBase($project, $from_user, $contents, $arr_token, $send_data);
        }
        // Can send email
        if ($is_send_email) {
            $to_user_mail = [];
            $cc_user_mail = [];
            $is_member = in_array($from_user->id, $list_member_id);
            $is_from_leader = in_array($from_user->id, $list_leader_id);
            // If user send is in group member
            if ($is_member) {
                $cc_user_mail = $from_user->email ? [$from_user->email] : [$from_user->email_user];
                $to_user_mail = array_map(function ($user) {
                    return $user->email;
                }, $list_leader);
            }
            // If user sending is admin or in the leader's group
            if ($is_from_leader || $from_user->role_group == 2) {
                $cc_user_id = $list_leader_id;
                $cc_user_mail = array_map(function ($id) {
                    $user = User::findOrFail($id);
                    return $user->email;
                }, $cc_user_id);
                if ($member_id != null) {
                    $to_user_mail = User::query()->where("id", $member_id)->get()->map(function ($user) {
                        return $user->email;
                    })->toArray();
                } else {
                    if (($key = array_search($from_user->id, $cc_user_id)) !== false) {
                        unset($cc_user_id[$key]);
                    }
                    $to_user_mail = $cc_user_mail;
                    $cc_user_mail = [$from_user->email];
                }
            }

            // Clear value null from array email
            $to_user_mail = array_filter($to_user_mail);
            $cc_user_mail = array_filter($cc_user_mail);

            //Clean array Mail-to and Mail-cc
            $same_mail = array_intersect($to_user_mail, $cc_user_mail);
            if (count($same_mail) != 0) {
                foreach ($same_mail as $mail) {
                    if (($k = array_search($mail, $to_user_mail)) !== false) {
                        unset($to_user_mail[$k]);
                    }
                }
            }
            $this->_sendMailTemplateBase($project, $is_from_leader, $from_user, $to_user_mail, $cc_user_mail, $contents);
        }
        return true;
    }

    // Function generate template mail
    private function _sendMailTemplateBase($project, $is_from_leader, $from_user, $to_user_mail, $cc_user_mail, $contents)
    {
        $subject_mail = "TB $project->NameVi [AKB]";
        $mail_name_from = $is_from_leader ? "Quản lý $from_user->FullName" : "$from_user->FullName";
        $mail_name_from .= " - " . Room::find($from_user->RoomId)->Name;
        $link = asset("/akb/work-task-detail") . "/" . $project->id;
        $content_mail = $this->_contentMail($from_user, $contents, $link);
        $attributes = [
            "subjectMail" => $subject_mail,
            "contentMail" => $content_mail,
            "arrMailAddressFrom" => config('mail.from.address'),
            "mailNameFrom" => $mail_name_from,
            "arrMailAddressTo" => $to_user_mail,
            "arrMailAddressCC" => $cc_user_mail
        ];
        SendEmail::dispatch("send_html", $attributes)->delay(now()->addMinute());
    }

    // Function generate template notification
    private function _sendNotificationTemplateBase($project, $from_user, $contents, $arr_token, $send_data)
    {
        $head_mess = "TB từ $project->NameVi";
        $body_noti = $this->_contentNotification($from_user, $contents);
        NotificationController::sendCloudMessaseNoti($head_mess, $arr_token, $body_noti, $send_data);
    }

    // Function return content notification general
    public function _contentNotification($from_user, $contents): string
    {
        $content_noti = "";
        $number = count($contents);
        foreach ($contents as $content) {
            switch ($content['action']) {
                case "insert":
                    $content_noti = "$from_user->FullName vừa thêm task $number mới.";
                    break;
                case "update":
                    $content_noti = "$from_user->FullName vừa cập nhật task: " . $content['task']->Name . ".";
                    break;
                case "assign":
                    $content_noti = "$from_user->FullName vừa giao $number task.";
                    break;
                case "delete":
                    $content_noti = "$from_user->FullName vừa xóa $number task.";
                    break;
                case "review":
                    $content_noti = "$from_user->FullName vừa yêu cầu review task: " . $content['task']->Name . ".";
                    break;
                case "review-again":
                    $content_noti = "$from_user->FullName vừa yêu cầu review lại task: " . $content['task']->Name . "[lần thứ " . $content['task']->NumberReturn . "]";
                    break;
                case "error":
                    $content_noti = "$from_user->FullName vừa báo lỗi task: " . $content['task']->Name . ".";
                    break;
                default:
                    break;
            }
        }
        return $content_noti;
    }

    // Function return content mail general
    private function _contentMail($from_user, $contents, $link): string
    {
        $i = 0;
        $j = 0;
        $room = Room::find($from_user->RoomId)->Name;
        $content_mail = "<p>Kính gửi Ông/Bà</p>";
        $content_mail .= "<p>$from_user->FullName - $room ";
        foreach ($contents as $content) {
            $task = $content["task"];
            $link_detail = $link . '?task=' . $task->id;
            switch ($content['action']) {
                case "insert":
                    if ($i == 0) {
                        $content_mail .= "vừa thêm mới các task.</p>";
                        $content_mail .= "<h4>Task mới:</h4>";
                        $content_mail .= "<ul>";
                        $i += 1;
                    }
                    $small = "<small>";
                    if ($content["task"]->StartDate) {
                        $small .= "<i>" . Carbon::parse($task->StartDate)->format(self::FOMAT_DMY) . ".</i>";
                    }
                    if ($content["task"]->EndDate) {
                        $small .= "<i>-" . Carbon::parse($task->EndDate)->format(self::FOMAT_DMY) . ".</i>";
                    }
                    $small .= "</small>";
                    $content_mail .= "<li><a style='color: green' href='${link_detail}' target='_blank'>$task->Name</a> $small</li>";
                    break;
                case "update":
                    $content_mail .= "vừa chỉnh sửa task <b>$task->Name</b>.</p>";
                    $content_mail .= "<h4>Nội dung chỉnh sửa:</h4>";
                    $content_mail .= "<ul>";
                    foreach ($content['messages'] as $mss) {
                        $content_mail .= "<li><a href='${link_detail}' target='_blank'>$mss.</a></li>";
                    }
                    break;
                case "assign":
                    if ($j == 0) {
                        $content_mail .= "vừa giao task mới.</p>";
                        $content_mail .= "<h4>Task được giao:</h4>";
                        $content_mail .= "<ul>";
                        $j += 1;
                    }
                    $small = "<small>";
                    if ($content["task"]->StartDate) {
                        $small .= "<i>" . Carbon::parse($content['task']->StartDate)->format(self::FOMAT_DMY) . ".</i>";
                    }
                    if ($content["task"]->EndDate) {
                        $small .= "<i>-" . Carbon::parse($content['task']->EndDate)->format(self::FOMAT_DMY) . ".</i>";
                    }
                    $small .= "</small>";
                    $content_mail .= "<li><a style='color: green' href='${link_detail}' target='_blank'>$task->Name</a> $small</li>";
                    break;
                case "delete":
                    if ($i == 0) {
                        $content_mail .= "vừa xóa các task.</p>";
                        $content_mail .= "<h4>Task xóa:</h4>";
                        $content_mail .= "<ul>";
                        $i += 1;
                    }
                    $name_task = $content["task"]->Name;
                    $content_mail .= "<li><a style='color: red;' href='${link}' target='_blank'>${name_task}</a></li>";
                    break;
                case "review":
                    $last_report = $task->dailyReports()->first();
                    $content_mail .= "<p>vừa yêu cầu review 1 task.</p>";
                    $content_mail .= "<h4>Task $task->Name:</h4>";
                    $content_mail .= "<ul>";
                    $content_mail .= "<li><p><b>Màn hình: </b>" . $last_report->ScreenName . "</p></li>";
                    $content_mail .= "<li><p><b>Nội dung: </b>" . $last_report->Contents . "</p></li>";
                    $content_mail .= "<li><p><b>Thời gian làm: </b>" . $last_report->WorkingTime . "h</p></li>";
                    $content_mail .= !is_null($last_report->Timedelay) ? "<li><p><b>Giờ trễ: </b>" . $last_report->Timedelay . "h</p></li>" : "";
                    $content_mail .= !is_null($last_report->Timesoon) ? "<li><p><b>Giờ vượt: </b>" . $last_report->Timesoon . "h</p></li>" : "";
                    $content_mail .= !is_null($last_report->Note) ? "<li><p><b>Ghi chú: </b>" . $last_report->Note . "</p></li>" : "";
                    break;
                case "review-again":
                    $last_report = $task->dailyReports()->first();
                    $error = ErrorReview::query()->where("WorkTaskID", $task->id)->orderByDesc("id")->first();
                    $content_mail .= "<p>vừa yêu cầu review lại 1 task.</p>";
                    $content_mail .= "<h4>Task $task->Name [lần thứ $task->NumberReturn]:</h4>";
                    $content_mail .= "<ul>";
                    $content_mail .= "<li><p><b>Mô tả lỗi: </b>" . $error->Descriptions . "</p></li>";
                    $content_mail .= "<li><p><b>Màn hình: </b>" . $last_report->ScreenName . "</p></li>";
                    $content_mail .= "<li><p><b>Nội dung: </b>" . $last_report->Contents . "</p></li>";
                    $content_mail .= "<li><p><b>Thời gian làm: </b>" . $last_report->WorkingTime . "h</p></li>";
                    $content_mail .= $last_report->Timedelay != null ? "<li><p><b>Giờ trễ: </b>" . $last_report->Timedelay . "h</p></li>" : "";
                    $content_mail .= $last_report->Timesoon != null ? "<li><p><b>Giờ vượt: </b>" . $last_report->Timesoon . "h</p></li>" : "";
                    $content_mail .= $last_report->Note != null ? "<li><p><b>Ghi chú: </b>" . $last_report->Note . "</p></li>" : "";
                    break;
                case "error":
                    $error = $task->errorReviews()->first();
                    $content_mail .= "<p>vừa báo lỗi 1 task.</p>";
                    $content_mail .= "<h4>Error Task $task->Name [lần thứ $task->NumberReturn]:</h4>";
                    $content_mail .= "<ul>";
                    $content_mail .= "<li><p><b>Mô tả: </b>" . $error->Descriptions . "</p></li>";
                    $content_mail .= $error->Note != null ? "<li><p><b>Ghi chú: </b>" . $error->Note . "</p></li>" : "";
                    break;
                default:
                    break;
            }
        }
        $content_mail .= "</ul>";
        $content_mail .= "<p>Chúc ông/bà một ngày làm việc hiệu quả, xin chân thành cảm ơn.</p>";
        $content_mail .= "<p>__</p>";
        $content_mail .= "<small>Để biết thêm thông tin chi tiết, ông/bà hãy truy cập vào <a href='$link' target='_blank'>Trang chủ.</a></small>";
        return $content_mail;
    }
}
