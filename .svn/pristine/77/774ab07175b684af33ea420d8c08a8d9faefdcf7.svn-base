<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\AdminController;

//fcm
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use App\Model\PushTokenList;

class NotificationController extends AdminController
{
    public static function sendCloudMessaseNoti($headrmess, $arrToken, $bodyNoti, $sendData, $dateTimeAction = null)
    {
        $arrTkUserId = [];

        foreach ($arrToken as $key => $value) {
            array_push($arrTkUserId, DB::table('push_token')->where('token_push', $value)->whereNull('deleted_at')->pluck('UserID')->first());
        }

        $arrForUserId = array_unique($arrTkUserId);

        foreach ($arrForUserId as $key => $value) {
            $objpushtoken = new PushTokenList;
            $objpushtoken->user_id = $value;
            $objpushtoken->title = $headrmess;
            $objpushtoken->message = $bodyNoti;
            $objpushtoken->id_item = $sendData['id'];
            $objpushtoken->screen = $sendData['data'];
            $objpushtoken->save();
        }

        try {
            $now = Carbon::now();
            if ($dateTimeAction != null && $dateTimeAction->diffInMinutes($now, false) > 0) {
                return;
            }

            if ($now->lte(Carbon::parse('today 4am')) && $now->gte(Carbon::parse('today 11pm'))) {
                return;
            }
        } catch (\Exception $e) {

        }

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder($headrmess);
        $notificationBuilder->setBody($bodyNoti)
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData($sendData);


        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $downstreamResponse = FCM::sendTo($arrToken, $option, $notification, $data);

        $downstreamResponse->numberSuccess();

        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();

        // return Array - you must remove all this tokens in your database
        $downstreamResponse->tokensToDelete();

        // return Array (key : oldToken, value : new token - you must change the token in your database)
        $downstreamResponse->tokensToModify();

        // return Array - you should try to resend the message to the tokens in the array
        $downstreamResponse->tokensToRetry();

        // return Array (key:token, value:error) - in production you should remove from your database the tokens
        $downstreamResponse->tokensWithError();
    }
}
