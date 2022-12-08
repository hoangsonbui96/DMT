<?php

namespace App\Http\Controllers\Admin\Equipment;

use App\Exports\EquipmentOfferExport;
use App\Http\Controllers\Admin\AdminController;
use App\MasterData;
use App\Menu;
use App\Project;
use App\RoleScreenDetail;
use App\RoleUserScreenDetailRelationship;
use App\Room;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Model\EquipmentOffer;
use App\Model\EquipmentOfferDetail;
use App\Http\Requests\EquipmentOfferRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\NotificationController;

/**
 * Controller screen Equipment Offer
 * Class EquipmentOfferController
 * @package App\Http\Controllers\Admin\Equipment
 */
class EquipmentOfferController extends AdminController
{
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;
    protected $app;
    protected $appr;
    protected $export;
    const KEYMENU = array(
        "add" => "EquipmentOfferAdd",
        "view" => "EquipmentOffer",
        "edit" => "EquipmentOfferEdit",
        "delete" => "EquipmentOfferDelete",
        "export" => "EquipmentOfferExport",
        "app" => "ListApprove",
        "appr" => "EquipmentOfferAppr"
    );
    const approved_list = array(0 => 'Chưa duyệt', 1 => 'Đã duyệt', 2 => 'Đã hủy');

    /**
     * EquipmentOfferController constructor.
     * @param Request $request
     * Check role view, insert, update
     */
    public function __construct(Request $request)
    {
        if (strpos(\Request::getRequestUri(), 'api') === false) {
            parent::__construct($request);
            $this->middleware('auth');
        }
        $array = $this->RoleView('EquipmentOffer', ['EquipmentOffer', 'EquipmentOffer', 'AbsenceListApprove']);
        $this->menu = $array['menu'];
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if ($value == $row->alias)
                    $this->$key = $row;
            }
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Exception
     */
    public function export(Request $request)
    {
        if (!isset($request->id)) {
            return $this->jsonErrors('Không có dữ liệu.');
        }
        $record = EquipmentOffer::find($request->id);
        if (isset($record)) {
            $record_details = EquipmentOfferDetail::where('EquipmentOfferID', $record->id)->get();
            if ($record_details->count() > 0) {
                return Excel::download(new EquipmentOfferExport($record, $record_details), 'Giay_de_nghi_thanh_toan.xlsx');
            }
        } else {
            return $this->jsonErrors('Không có dữ liệu.');
        }
    }

    /**
     * Get data working schedule
     * @param Request $request
     * @param string $orderBy
     * @param string $sortBy
     * @return View screen (working-schedule)
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Request $request, $orderBy = 'OfferDate', $sortBy = 'desc')
    {
        $this->authorize('view', $this->menu);
        $this->getRecordWithRequest($request, $orderBy, $sortBy);

        $this->data['request'] = $request->query();
        $this->data['master_datas'] = $this->getReasonAbsence();
        $this->data['users'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        $this->data['equipment_offer_detail'] = EquipmentOfferDetail::all();
        $this->data['add'] = $this->add;
        $this->data['edit'] = $this->edit;
        $this->data['delete'] = $this->delete;
        $this->data['export'] = $this->export;
        $this->data['appr'] = $this->appr;

        if (strpos(\Request::getRequestUri(), 'api') !== false) {
            return response()->json(['data' => $this->data]);
        }
        return $this->viewAdminLayout('equipment.equipment-offer', $this->data);
    }

    /**
     * @param null $id
     * @param null $del
     * @return View popup (equipment-offer-detail)
     */
    public function showDetail($id = null, $del = null)
    {
        $this->data['users'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        $this->data['master_datas'] = $this->getReasonAbsence();
        $this->data['boolean'] = 1;
        $this->data['approved_list'] = self::approved_list;
        $this->data['add'] = $this->add;
        $this->data['approve'] = $this->appr;
        $this->data['project'] = Project::all();
        if ($id != null) {
            $one = EquipmentOffer::find($id);
            $one_details = EquipmentOfferDetail::where('EquipmentOfferID', '=', $one->id)->get();
            $this->data['equipment_offer_info'] = $one;
            $this->data['equipment_offer_detail'] = $one_details;
            if ($del == 'del') {
                if ($one != null) {
                    $one->delete();
                    if ($one_details != null) {
                        foreach ($one_details as $one_detail) {
                            $one_detail->delete();
                        }
                    }
                    if (strpos(\Request::getRequestUri(), 'api') !== false) {
                        return response()->json(['success' => 'Xóa thành công.']);
                    }
                }
                return 1;
            }
            if ($this->data['equipment_offer_info']) {
                if (strpos(\Request::getRequestUri(), 'api') !== false) {
                    return $this->data;
                }
                return $this->viewAdminIncludes('equipment.equipment-offer-detail', $this->data);
            } else {
                return "";
            }
        } else {
            if (strpos(\Request::getRequestUri(), 'api') !== false) {
                return $this->data;
            }
            return $this->viewAdminIncludes('equipment.equipment-offer-detail', $this->data);
        }
    }

    /**
     * @param EquipmentOfferRequest $request
     * @param null $id
     * @return string|void
     */
    public function store(EquipmentOfferRequest $request, $id = null)
    {
//        DB::enableQueryLog();
        if (count($request->input()) === 0) {
            return abort('404');
        }
        try {

            $data = [
                'offer_user_id' => 'OfferUserID',
                'offer_date' => 'OfferDate',
                'project_id' => 'ProjectID',
                'content' => 'Content',
            ];

            $data_detail = [
                'description' => 'Description',
                'quantity' => 'Quantity',
                'unit_price' => 'UnitPrice',
                'final_unit_price' => 'FinalUnitPrice',
                'price' => 'Price',
                'buy_address' => 'BuyAddress',
                'buy_date' => 'BuyDate',
                'buy_user_id' => 'BuyUserID',
            ];

            $modeIsUpdate = array_key_exists('id', $request->input());
            $one = !$modeIsUpdate ? new EquipmentOffer() : EquipmentOffer::find($request->id);

            foreach ($data as $key => $value) {
                if (isset($request->$key) && $request->$key != '') {
                    if ($key == 'offer_date') {
                        $request->$key = $this->fncDateTimeConvertFomat($request->$key, 'd/m/Y', 'Y-m-d');
                    }
                    $one->$value = $request->$key;
                }
            }
            if (!$modeIsUpdate) {
                $one->Approved = 0;
                $one->ApprovedUserID = 0;
            } else {
                $one->UserUpdateID = Auth::user()->id;
            }

            $one->save();
            //firebase notication
            $arrUserMail = explode(',', MasterData::query()->where('DataValue', '=', 'EM003')->pluck('DataDescription')->toArray()[0]);
            $arrUIDs = DB::table('users')->whereIn('email', $arrUserMail)->whereNull('deleted_at')->pluck('id')->toArray();
            $arrToken = DB::table('push_token')->whereIn('UserID', $arrUIDs)->whereNull('deleted_at')->where('allow_push', 1)->pluck('token_push')->toArray();

            if (count($arrToken) > 0) {
                $sendData = [];
                $sendData['id'] = $one->id;
                $sendData['data'] = "MX";
                if ($request['id'] === null) {
                    $headrmess = "Đề xuất mua sắm mới. " . $request['offer_date'];
                } else {
                    $headrmess = "Đề xuất mua sắm cập nhật. " . $request['offer_date'];
                }
                $bodyNoti = "Nội dung: " . $request['content'];
                NotificationController::sendCloudMessaseNoti($headrmess, $arrToken, $bodyNoti, $sendData);
            }

            if ($modeIsUpdate) {
                $all_detail = EquipmentOfferDetail::where('EquipmentOfferID', $one->id)->get();
                if ($all_detail) {
                    foreach ($all_detail as $detail) {
                        if (!in_array($detail->id, $request->detail_id)) {
                            $detail->delete();
                        }

                    }
                }
            }

            foreach ($request->description as $key => $item) {
                if (isset($item) && $item != '') {
                    $modeIsUpdateDetail = false;
                    if (isset($request->detail_id) && count($request->detail_id) > 0 && isset($request->detail_id[$key])) {
                        $modeIsUpdateDetail = true;
                    }

                    $one_detail = !$modeIsUpdateDetail ? new EquipmentOfferDetail() : EquipmentOfferDetail::find($request->detail_id[$key]);

                    foreach ($data_detail as $title => $value) {
                        if (isset($request->$title) && count($request->$title) > 0 && isset($request->$title[$key])) {
                            if ($title == 'buy_date') {
                                $one_detail->$value = $this->fncDateTimeConvertFomat($request->$title[$key], 'd/m/Y', 'Y-m-d H:i:s');
                            } elseif ($title == 'description' || $title == 'buy_address') {
                                $one_detail->$value = strip_tags($request->$title[$key]);
                            } else {
                                $one_detail->$value = $request->$title[$key];
                            }
                        } else {
                            if ($title == 'buy_date') {
                                $one_detail->$value = '0000-00-00';
                            }
                        }
                    }
                    if ($modeIsUpdateDetail) {
                        if (isset($request->status) && count($request->status) > 0 && in_array($request->detail_id[$key], $request->status)) {
                            $one_detail->Status = 2;
                        } else {
                            $one_detail->Status = 0;
                        }
                    }
                    $one_detail->EquipmentOfferID = $one->id;
                    $one_detail->save();
                }
            }

            if (!$one) {
                return $this->jsonErrors(__('admin.error.save'));
            } else {
                $this->sendMail($request, $one);
                if (strpos(\Request::getRequestUri(), 'api') !== false) {
                    return $this->jsonSuccess(__('admin.success.save'));
                }

                return $this->jsonSuccessWithRouter('admin.EquipmentOffer');
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Duyệt/từ chối đề xuất mua đồ
     * @param Request $request
     * @param null $id
     * @param null $del
     * @return int|string
     */
    public function AprEquipmentOffer(Request $request, $id = null, $del = null)
    {
        $this->data['request'] = $request->query();
        if ($id != null) {
            $one = EquipmentOffer::find($id);
            if ($one) {
                $one->ApprovedUserID = Auth::user()->id;
                $one->ApprovedDate = Carbon::now();
                if ($del == 'del') {
                    if ($this->data['request']['Comment'] == '') {
                        return $this->jsonErrors('Vui lòng điền lý do');
                    }
                    $one->Approved = 2;
                    $one->Note = $this->data['request']['Comment'];
                    $one->save();

                    //firebase notication
                    $aId = DB::table('equipment_offer')->where('id', $id)->pluck('OfferUserID')->first();
                    $arrToken = $arrToken = DB::table('push_token')->where('UserID', $aId)->whereNull('deleted_at')->pluck('token_push')->toArray();
                    if (count($arrToken) > 0) {
                        $sendData = [];
                        $sendData['id'] = $id;
                        $sendData['data'] = "MX";
                        $headrmess = "Đề xuất mua sắm từ chối. " . $request['offer_date'];
                        $bodyNoti = "Từ chối: " . Auth::user()->FullName;
                        NotificationController::sendCloudMessaseNoti($headrmess, $arrToken, $bodyNoti, $sendData);
                    }

                    $this->sendMail($request, $one, false, true);
                    return $this->jsonSuccess('Đã từ chối');
                } else {
                    $one->Approved = 1;
                    $one->save();

                    //firebase notication
                    $aId = DB::table('equipment_offer')->where('id', $id)->pluck('OfferUserID')->first();
                    $arrToken = $arrToken = DB::table('push_token')->where('UserID', $aId)->whereNull('deleted_at')->pluck('token_push')->toArray();
                    if (count($arrToken) > 0) {
                        $sendData = [];
                        $sendData['id'] = $id;
                        $sendData['data'] = "MX";
                        $headrmess = "Đề xuất mua được duyệt. " . $request['offer_date'];
                        $bodyNoti = "Người duyệt: " . Auth::user()->FullName;
                        NotificationController::sendCloudMessaseNoti($headrmess, $arrToken, $bodyNoti, $sendData);
                    }

                    $this->sendMail($request, $one, true);
                    return $this->jsonSuccess('Duyệt thành công');
                }
            } else {
                return $this->jsonErrors('Duyệt thất bại');
            }
        } else {
            return $this->viewAdminIncludes('equipment.equipment-offer', $this->data);
        }
    }

    /**
     * Send data mail to serve
     * @param $array
     * @param $header
     * @param null $comment
     * @return bool
     */
    public function sendMail($request, $one, $appr = false, $unAppr = false)
    {
        $offerUser = User::find($one->OfferUserID);
        $room = Room::find($offerUser->RoomId);
        if ($one->ProjectID && $one->ProjectID != 0) {
            $project = Project::find($one->ProjectID);
            $project = 'Dự án: ' . $project->NameVi . '<br/><br>';
        } else {
            $project = '';
        }

        $complete = false;
        $complete_count = 0;
        if (isset($request->buy_date) && count($request->buy_date) > 0) {
            foreach ($request->buy_date as $key => $buy_date) {
                if ($buy_date != '' && $buy_date != null) {
                    $complete_count += 1;
                } elseif (isset($request->status) && count($request->status) > 0 && in_array($request->detail_id[$key], $request->status)) {
                    $complete_count += 1;
                }
            }
            if ($complete_count == count($request->buy_date)) {
                $complete = true;
            }
        }

        $arrMailTO = array();
        $arrMailCC = array();
        $mailCC = MasterData::query()->where('DataValue', '=', 'EM003')->first();
        $replace_mailTO = MasterData::query()->where('DataValue', '=', 'EM006')->first();

        $modeIsUpdate = array_key_exists('id', $request->input());

        if ($modeIsUpdate || $appr || $unAppr) {
            $subjectMail = 'TB cập nhật đề xuất mua sắm (' . $one->OfferDate . ')';
        } else if ($complete) {
            $subjectMail = 'TB hoàn thành đơn đề xuất mua sắm (' . $one->OfferDate . ')';
        } else {
            $subjectMail = 'TB đề xuất mua sắm (' . $one->OfferDate . ')';
        }

        $contentMail = 'Kính gửi Ban Giám Đốc <br/><br/>';
        $contentMail .= "<div style='display:block'>";

        if ($appr) {
            $apprUser = User::find($one->ApprovedUserID);

            $contentMailBody = ' Đơn đề xuất mua sắm của ' . $offerUser['FullName'] . ' có số phiếu mua là: ' . self::createOfferNumber($one->id) . ' đã được ' . $apprUser['FullName'] . ' duyệt. <br/>';
            $contentMailBody .= ' Nếu bạn có thắc mắc hay muốn thay đổi nội dung, vui lòng liên hệ với văn phòng. <br/>';
            $contentMailBody .= "</div><br/>";
            $contentMailBody .= "Xin cảm ơn.<br/>";

            if ($offerUser->email !== null) {
                array_push($arrMailTO, $offerUser->email);

            } else {
                if ($replace_mailTO) {
                    $arrMailTO = explode(',', $replace_mailTO->DataDescription);
                }
            }

            if ($mailCC) {
                $arrMailCC = explode(',', $mailCC->DataDescription);
            }

            $list_user = RoleUserScreenDetailRelationship::query()
                ->select('user_id')
                ->where('permission', '=', 1)
                ->where('screen_detail_alias', 'like', 'EquipmentOfferAppr')
                ->get();

            if ($list_user) {
                foreach ($list_user as $_list_user) {
                    if ($_list_user->user_id == $one->offer_user_id) {
                        continue;
                    }
                    $apprUser = User::find($_list_user->user_id);

                    if ($apprUser && $apprUser->email !== null) {
                        array_push($arrMailCC, $apprUser->email);
                    }
                }
            }
        } elseif ($unAppr) {
            $apprUser = User::find($one->ApprovedUserID);
            $contentMailBody = ' Đơn đề xuất mua sắm của ' . $offerUser['FullName'] . ' có số phiếu mua là: ' . self::createOfferNumber($one->id) . ' đã bị ' . $apprUser['FullName'] . ' từ chối. <br/>';
            $contentMailBody .= ' Nếu bạn có thắc mắc, vui lòng liên hệ với văn phòng.';
            $contentMailBody .= "</div><br/>";
            $contentMailBody .= "Xin cảm ơn.<br/>";

            if ($offerUser->email !== null) {
                array_push($arrMailTO, $offerUser->email);

            } else {
                if ($replace_mailTO) {
                    $arrMailTO = explode(',', $replace_mailTO->DataDescription);
                }
            }

            if ($mailCC) {
                $arrMailCC = explode(',', $mailCC->DataDescription);
            }

            $list_user = RoleUserScreenDetailRelationship::query()
                ->select('user_id')
                ->where('permission', '=', 1)
                ->where('screen_detail_alias', 'like', 'EquipmentOfferAppr')
                ->get();

            if ($list_user) {
                foreach ($list_user as $_list_user) {
                    if ($_list_user->user_id == $one->offer_user_id) {
                        continue;
                    }
                    $apprUser = User::find($_list_user->user_id);

                    if ($apprUser && $apprUser->email !== null) {
                        array_push($arrMailCC, $apprUser->email);
                    }
                }
            }
        } elseif ($complete) {
            $contentMailBody = ' Đơn đề xuất mua sắm của ' . $offerUser['FullName'] . ' có số phiếu mua là: ' . self::createOfferNumber($one->id) . ' đã hoàn thành. <br/>';
            $contentMailBody .= 'Vào ngày ' . Carbon::now()->format(self::FOMAT_DISPLAY_DMY) . '.';
            $contentMailBody .= "</div><br/>";
            $contentMailBody .= "Xin cảm ơn.<br/>";

            if ($offerUser->email !== null) {
                array_push($arrMailTO, $offerUser->email);
            } else {
                if ($replace_mailTO) {
                    $arrMailTO = explode(',', $replace_mailTO->DataDescription);
                }
            }

            if ($mailCC) {
                $arrMailCC = explode(',', $mailCC->DataDescription);
            }

            $list_user = RoleUserScreenDetailRelationship::query()
                ->select('user_id')
                ->where('permission', '=', 1)
                ->where('screen_detail_alias', 'like', 'EquipmentOfferAppr')
                ->get();

            if ($list_user) {
                foreach ($list_user as $_list_user) {
                    if ($_list_user->user_id == $one->offer_user_id) {
                        continue;
                    }
                    $apprUser = User::find($_list_user->user_id);

                    if ($apprUser && $apprUser->email !== null) {
                        array_push($arrMailCC, $apprUser->email);
                    }
                }
            }
        } else {
            $mailBody = "<div style='display:table'>";
            $mailBody .= "<table cellpadding='10' cellspacing='0' border='1'";
            $mailBody .= "<thead>";
            $mailBody .= "<tr>";
            $mailBody .= "<th>" . __('admin.stt') . "</th>";
            $mailBody .= "<th>" . __('admin.equipment-offer.description') . "</th>";
            $mailBody .= "<th>" . __('admin.equipment.quantity') . "</th>";
            $mailBody .= "<th>" . __('admin.equipment-offer.price') . "</th>";
            $mailBody .= "</tr>";
            $mailBody .= "</thead>";
            $mailBody .= "<tbody>";
            $total = 0;
            foreach ($request->description as $key => $item) {
                if (isset($request->status) && count($request->status) > 0 && in_array($request->detail_id[$key], $request->status)) {
                    $total_price = 0;
                } else {
                    $total_price = $request->price[$key];
                }
                $mailBody .= "<tr>";
                $mailBody .= "<td style='text-align: center'>" . ($key + 1) . "</td>";
                $mailBody .= "<td>" . ($item) . "</td>";
                $mailBody .= "<td>" . ($request->quantity[$key]) . "</td>";
                $mailBody .= "<td style='text-align: right'>" . number_format($total_price, 0, '.', ',') . " VNĐ</td>";
                $mailBody .= "</tr>";
                $total += $request->price[$key];
            }
            $mailBody .= "</tbody>";
            $mailBody .= "</table>";
            $mailBody .= "<div><br>Tổng: " . number_format($total, 0, '.', ',') . " VNĐ </div>";
            $mailBody .= "</div>";

            if ($modeIsUpdate) {
                $contentMailBody = ' Đơn đề xuất mua sắm có số phiếu mua là: ' . self::createOfferNumber($one->id) . ' đã được cập nhật với nội dung như sau:<br/><br>';
                $contentMailBody .= ' Họ và tên: ' . $offerUser['FullName'] . (isset($room) ? (' - ' . $room->Name) : '') . ' <br/><br>';
                $contentMailBody .= $project;
                $contentMailBody .= ' Nội dung: ' . $one->Content . '<br/><br>';
                $contentMailBody .= $mailBody;
                $contentMailBody .= "</div><br/>";
                $contentMailBody .= "Tôi xin chân thành cảm ơn.<br/>";
            } else {
                $contentMailBody = 'Tôi xin phép được đề xuất mua sắm với nội dung như sau: <br/><br>';
                $contentMailBody .= ' Họ và tên: ' . $offerUser['FullName'] . (isset($room) ? (' - ' . $room->Name) : '') . ' <br/><br>';
                $contentMailBody .= ' Số phiếu mua: ' . self::createOfferNumber($one->id) . ' <br/><br>';
                $contentMailBody .= $project;
                $contentMailBody .= ' Với nội dung: ' . $one->Content . '<br/><br>';
                $contentMailBody .= $mailBody;
                $contentMailBody .= "</div><br/>";
                $contentMailBody .= "Tôi xin chân thành cảm ơn.<br/>";
            }

            if ($mailCC) {
                $arrMailCC = explode(',', $mailCC->DataDescription);
            }

            if ($offerUser->email !== null) {
                array_push($arrMailCC, $offerUser->email);
            }

            $list_user = RoleUserScreenDetailRelationship::query()
                ->select('user_id')
                ->where('permission', '=', 1)
                ->where('screen_detail_alias', 'like', 'EquipmentOfferAppr')
                ->get();

            if ($list_user) {
                foreach ($list_user as $_list_user) {
                    if ($_list_user->user_id == $one->offer_user_id) {
                        continue;
                    }
                    $apprUser = User::find($_list_user->user_id);

                    if ($apprUser && $apprUser->email !== null) {
                        array_push($arrMailTO, $apprUser->email);
                    }
                }
            }
        }

        $contentMail .= $contentMailBody;

        $arrMailCC = array_diff($arrMailCC, $arrMailTO);

        if (!$complete && $complete_count > 0) {

        } else {
            $this->SendMailHtml($subjectMail, $contentMail, config('mail.from.address'), $offerUser['FullName'] . (isset($room) ? (' - ' . $room->Name) : ''), $arrMailTO, $arrMailCC);
        }
    }

    /**
     * @param $request
     * @param $orderBy
     * @param $sortBy
     * @param null $export
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder|\Illuminate\Database\Query\Builder[]|\Illuminate\Http\RedirectResponse|\Illuminate\Support\Collection
     * @throws \Exception
     */
    public function getRecordWithRequest($request, $orderBy, $sortBy, $export = null)
    {
//        DB::enableQueryLog();
        $recordPerPage = $this->getRecordPage();
        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy == 'desc' ? 'asc' : 'desc') . "/" . $query_string;
        $sort = ($sortBy == 'asc' ? 'asc' : 'desc');
        parse_str(str_replace('?', '', $query_string), $query_array);

        $user = Auth::user();
        $user_id = $user->role_group != 2 ? $user->id : null;

        $equipment_offer = EquipmentOffer::query()
            ->select('equipment_offer.*',
                'equipment_offer_detail.Description', 'equipment_offer_detail.Quantity', 'equipment_offer_detail.UnitPrice',
                'equipment_offer_detail.FinalUnitPrice', 'equipment_offer_detail.Price', 'equipment_offer_detail.BuyAddress',
                'equipment_offer_detail.BuyDate', 'equipment_offer_detail.Status', 'tb1.FullName', 'tb2.FullName', 'tb3.FullName')
            ->leftJoin('equipment_offer_detail', 'equipment_offer.id', '=', 'equipment_offer_detail.EquipmentOfferID')
            ->leftJoin('users as tb1', 'equipment_offer.OfferUserID', '=', 'tb1.id')
            ->leftJoin('users as tb2', 'equipment_offer.ApprovedUserID', '=', 'tb2.id')
            ->leftJoin('users as tb3', 'equipment_offer_detail.BuyUserID', '=', 'tb3.id');

        if ($user_id) {
            $equipment_offer->where('OfferUserID', '=', $user_id);
        }

        $this->data['request'] = $request->query();
        // Search in columns
        $one = EquipmentOffer::query()->select('equipment_offer.OfferDate', 'equipment_offer.ApprovedDate',
            'equipment_offer_detail.Description', 'equipment_offer_detail.Quantity', 'equipment_offer_detail.UnitPrice',
            'equipment_offer_detail.FinalUnitPrice', 'equipment_offer_detail.Price', 'equipment_offer_detail.BuyAddress', 'equipment_offer_detail.BuyDate',
            'tb1.FullName', 'tb2.FullName', 'tb3.FullName')
            ->leftJoin('equipment_offer_detail', 'equipment_offer.id', '=', 'equipment_offer_detail.EquipmentOfferID')
            ->leftJoin('users as tb1', 'equipment_offer.OfferUserID', '=', 'tb1.id')
            ->leftJoin('users as tb2', 'equipment_offer.ApprovedUserID', '=', 'tb2.id')
            ->leftJoin('users as tb3', 'equipment_offer_detail.BuyUserID', '=', 'tb3.id')->first();
        if ($one) {
            $one = $one->toArray();
            if (array_key_exists('search', $request->input())) {
                $equipment_offer = $equipment_offer->where(function ($query) use ($one, $request, $user_id) {

                    foreach ($one as $key => $value) {
                        if ($key == 'FullName') {
                            if ($user_id) {
                                $query->orWhere('tb2.' . $key, 'like', '%' . $request->input('search') . '%')
                                    ->orWhere('tb3.' . $key, 'like', '%' . $request->input('search') . '%');
                            } else {
                                $query->orWhere('tb1.' . $key, 'like', '%' . $request->input('search') . '%')
                                    ->orWhere('tb2.' . $key, 'like', '%' . $request->input('search') . '%')
                                    ->orWhere('tb3.' . $key, 'like', '%' . $request->input('search') . '%');
                            }
                        } elseif ($key == 'Description' || $key == 'BuyAddress') {
                            $query->orWhere('equipment_offer_detail.' . $key, 'like', '%' . $request->input('search') . '%');
                        } elseif (in_array($key, ['Quantity', 'UnitPrice', 'FinalUnitPrice', 'Price'])) {
                            try {
                                $a = (double)$request->input('search');
                                if ($a !== 0.0) {
                                    $query->orWhere('equipment_offer_detail.' . $key, '=', $a);
                                }
                            } catch (\Exception $e) {

                            }
                        } elseif ($key == 'BuyDate') {
                            $query->orWhereRaw('(DATE_FORMAT(equipment_offer_detail.' . $key . ',"%d/%m/%Y")) LIKE ?', '%' . $request->input('search') . '%');
                        } else {
                            $strSearch = trim($this->convert_vi_to_en($request->input('search')));
                            if (in_array($key, ['OfferDate', 'ApprovedDate'])) {
                                $query->orWhereRaw('(DATE_FORMAT(equipment_offer.' . $key . ',"%d/%m/%Y")) LIKE ?', '%' . $strSearch . '%');
                            } else {
                                $query->orWhere('equipment_offer.' . $key, 'LIKE', '%' . $strSearch . '%');
                            }
                        }
                    }
                });
            }
        }

        //check value request search
        if ($request->has('Date')) {
            if (\DateTime::createFromFormat('d/m/Y', $request['Date'][0]) === FALSE && $request['Date'][0] != '' ||
                \DateTime::createFromFormat('d/m/Y', $request['Date'][1]) === FALSE && $request['Date'][1] != '') {
                return Redirect::back();
            }
        }

        if ($request['UID'] != '') {
            $equipment_offer = $equipment_offer->where(function ($query) use ($request, $user_id) {
                if ($user_id) {
                    $query->Where('equipment_offer.ApprovedUserID', $request['UID'])
                        ->orWhere('equipment_offer_detail.BuyUserID', $request['UID']);
                } else {
                    $query->where('equipment_offer.OfferUserID', $request['UID'])
                        ->orWhere('equipment_offer.ApprovedUserID', $request['UID'])
                        ->orWhere('equipment_offer_detail.BuyUserID', $request['UID']);
                }
            });
        }

        foreach ($this->data['request'] as $key => $value) {
            if (is_array($value)) {
                $value[0] != '' ? $value[0] = $this->fncDateTimeConvertFomat($value[0], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD) : $value[0];
                $value[1] != '' ? $value[1] = $this->fncDateTimeConvertFomat($value[1], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD) : $value[1];

                $equipment_offer->where(function ($query) use ($value) {
                    if ($value[0] != '' && $value[1] != '' && $value[0] !== $value[1]) {
                        $query = $query->whereBetween('equipment_offer.OfferDate', array(Carbon::parse($value[0])->startOfDay()->toDateString(), Carbon::parse($value[1])->endOfDay()->toDateString()));
                    }
                    if ($value[0] === $value[1] && $value[0] != '') {
                        $query = $query->whereRaw("CAST(equipment_offer.OfferDate AS DATE) = '$value[0]'");
                    }
                    if ($value[0] != '' && $value[1] == '') {
                        $query = $query->where('equipment_offer.OfferDate', '>=', Carbon::parse($value[0])->startOfDay()->toDateString());
                    }
                    if ($value[0] == '' && $value[1] != '') {
                        $query = $query->where('equipment_offer.OfferDate', '<=', Carbon::parse($value[1])->startOfDay()->toDateString());
                    }
                });
            }
        }
        if (isset($request['approved']) && $request['approved'] != 'null' && $request['approved'] != '' && $request['approved'] < 3) {
            $equipment_offer = $equipment_offer->where(function ($query) use ($request) {
                $query->where('equipment_offer.Approved', '=', $request['approved']);
            });
        }

        $list_id = array();
        $equipment_offer = $equipment_offer->get();
        if ($equipment_offer) {
            foreach ($equipment_offer as $item) {
                if (isset($request['approved']) && $request['approved'] == 3) {
                    if ($item->Approved == 1 && $item->BuyDate == '0000-00-00' && !in_array($item['id'], $list_id) && $item->Status != 2) {
                        $list_id[] = $item->id;
                    }
                } elseif (isset($request['approved']) && $request['approved'] == 4) {
                    if (($item->BuyDate != '0000-00-00' && $item->Approved == 1) || $item->Status == 2) {
                        $list_id[] = $item->id;
                    } else {
                        if ($list_id && in_array($item->id, $list_id)) {
                            $key = array_search($item->id, $list_id);
                            unset($list_id[$key]);
                        }
                    }
                    $list_id = array_unique($list_id);
                } else {
                    if (!in_array($item->id, $list_id)) {
                        $list_id[] = $item->id;
                    }
                }
            }
        }
//        $list_id = array_unique(array_column($equipment_offer, 'id'));

        $equipment_offer = EquipmentOffer::whereIn('id', $list_id);

        if (Schema::hasColumn('equipment_offer', $orderBy)) {
            $equipment_offer = $equipment_offer->orderBy($orderBy, $sortBy);
        }

        $equipment_offer = $equipment_offer->orderBy('id', 'DESC');


        //redirect to the last page if current page has no record
        if ($equipment_offer->count() == 0) {
            if (array_key_exists('page', $query_array)) {
                if ($query_array['page'] > 1) {
                    $query_array['page'] = $equipment_offer->lastPage();
                    $query_string = http_build_query($query_array);
                    $fullUrl = $request->url() . '?' . $query_string;
                    return redirect($fullUrl);
                }
            }
        }

        if ($export != '' || $export != null) {
            return $equipment_offer->get();
        }
        $equipment_offer = $equipment_offer->paginate($recordPerPage);
        $count = $equipment_offer->count();

        $this->data['stt'] = $this->numericalOrderSort($query_array, $recordPerPage, $sort, $count);
        $this->data['equipment_offer'] = $equipment_offer;
        foreach ($this->data['equipment_offer'] as $item) {
            $item->totalPrice = EquipmentOfferDetail::query()->where('EquipmentOfferID', '=', $item->id)->where('Status', '!=', 2)->sum('Price');
            $item->countAll = EquipmentOfferDetail::query()->where('EquipmentOfferID', '=', $item->id)->where('Status', '!=', 2)->count('id');
            $item->countBuy = EquipmentOfferDetail::query()->where('EquipmentOfferID', '=', $item->id)->where('Status', '!=', 2)->where('BuyDate', '!=', '0000-00-00')->count('id');
            $item->buyDate = EquipmentOfferDetail::query()->select('BuyDate')->where('EquipmentOfferID', '=', $item->id)->where('BuyDate', '!=', '0000-00-00')->orderBy('BuyDate', 'desc')->first();
        }
        $this->data['query_array'] = $query_array;
        $this->data['sort_link'] = $sort_link;
        $this->data['sort'] = $sort;

        $this->data['equipment_offer']->totalUnitPrice = 0;
        $this->data['equipment_offer']->totalFinalPrice = 0;
        $this->data['equipment_offer']->totalUnitNumber = 0;
        $this->data['equipment_offer']->totalFinalNumber = 0;

        $this->data['equipment_offer']->totalFinalPrice = EquipmentOfferDetail::query()
            ->whereIn('EquipmentOfferID', $list_id)
            ->where('Status', '!=', 2)
            ->where('FinalUnitPrice', '!=', '')
            ->sum('Price');
        $this->data['equipment_offer']->totalFinalNumber = EquipmentOffer::query()
            ->whereIn('id', $list_id)
            ->where('Approved', '=', 1)
            ->count();
        $this->data['equipment_offer']->totalUnitPrice = EquipmentOfferDetail::query()
            ->whereIn('EquipmentOfferID', $list_id)
            ->where('Status', '!=', 2)
            ->where('FinalUnitPrice', '==', '')
            ->sum('Price');
        $this->data['equipment_offer']->totalUnitNumber = EquipmentOffer::query()
            ->whereIn('id', $list_id)
            ->where('Approved', '=', 0)
            ->count();
        // return  $this->data;
    }

    public static function createOfferNumber($id)
    {
        $offer_number = (string)$id;
        while (strlen($offer_number) < 9) {
            $offer_number = '0' . $offer_number;
        }
        return $offer_number;
    }
}
