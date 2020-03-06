<?php

use app\helpers\Translate;
use idbyii2\helpers\IdbAccountId;
use yii\helpers\Html;
use yii\helpers\Url;

function getMessagesTitle($number)
{
    return Translate::_(
        'people',
        'You have {count,number} {count, plural, =1{message} other{messages}}.',
        ['count' => $number]
    );
}

function getNotificationTitle($number)
{
    return Translate::_(
        'people',
        'You have {count_notifications,number} {count_notifications, plural, =0{notifications} =1{notification} other{notifications}}',
        ['count_notifications' => $number]
    );
}

function getUploadRequestsTitle($number)
{
    return Translate::_(
        'people',
        'You have {count_requests,number} {count_requests, plural, =0{upload requests} =1{upload request} other{upload requests}}',
        ['count_requests' => $number]
    );
}

function setButtonIcon($type)
{
    $button_type = null;
    $icon = null;
    $return = [];

    switch ($type) {
        case 'green':
            $button_type = 'kt-font-success';
            $icon = 'success';
            break;
        case 'amber':
            $button_type = 'kt-font-warning';
            $icon = 'warning';
            break;
        case 'red':
            $button_type = 'kt-font-danger';
            $icon = 'error';
            break;
    }

    $return[0] = $button_type;
    $return[1] = $icon;

    return $return;
}

$uploadRequests = [];
if(!empty(Yii::$app->view->params['uploadRequests'])) {
    $uploadRequests = Yii::$app->view->params['uploadRequests'];
}
$notificationsJS = [];
$notifications = [];
$countNotifications = 0;
if (!empty(Yii::$app->view->params['notifications'])) {
    $notifications = Yii::$app->view->params['notifications'];
    $countNotifications = count($notifications);
    foreach ($notifications as $notification) {
        $notificationData = json_decode($notification->data, true);
        if (!empty($notificationData['title'])) {
            $notificationData['title'] = htmlspecialchars(htmlentities($notificationData['title'], ENT_QUOTES, "UTF-8"));
        }
        if (!empty($notificationData['body'])) {
            $notificationData['body'] = htmlspecialchars(htmlentities($notificationData['body'], ENT_QUOTES, "UTF-8"));
        }
        if(!empty($notificationData['metadata'])) {
            $notificationData['metadata'] = json_decode($notificationData['metadata'], true);
        }
        $notificationsJS[$notification->type][] = [

            'data' => $notificationData,
            'id' => $notification->id
        ];
    }
}


$countMessages = 0;
if (!empty(Yii::$app->view->params['messages'])) {
    $messages = Yii::$app->view->params['messages'];
    $countMessages = count($messages);
}

$messagesRing = Yii::$app->view->params['messagesRing'] ?? false;
$messagesRing =  count($uploadRequests) > 0? true: $messagesRing;

?>
<div class="kt-header__topbar-item dropdown">
    <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px,0px">

    <span class="kt-header__topbar-icon kt-pulse kt-pulse--light">
        <i class="flaticon2-bell-alarm-symbol <?= ($messagesRing) ? 'kt-font-danger' : '' ?>"></i>
        <span class="<?= ($messagesRing) ? '' : 'kt-hidden' ?> kt-pulse__ring"></span>
    </span>

    </div>
    <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-xl">
        <form>

            <!--begin: Head -->
            <div class="kt-head kt-head--skin-light kt-head--fit-x kt-head--fit-b">
                <h3 class="kt-head__title">
                    <?= Translate::_('people', 'User notifications') ?>
                    <hr>
                    <span class="btn btn-label-primary btn-sm btn-bold btn-font-md">
                        <?= getMessagesTitle($countMessages) ?>
                    </span>
                    <span class="btn btn-label-primary btn-sm btn-bold btn-font-md">
                        <?= getNotificationTitle($countNotifications) ?>
                    </span>
                    <span style="margin-top: 4px;" class="btn btn-label-primary btn-sm btn-bold btn-font-md">
                        <?= getUploadRequestsTitle(count($uploadRequests)) ?>
                    </span>
                </h3>
                <ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-brand  kt-notification-item-padding-x"
                    role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active show" data-toggle="tab" href="#topbar_notifications_messages"
                           role="tab" aria-selected="true"><?= Translate::_('people', 'Messages'); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#topbar_notifications_notifications" role="tab"
                           aria-selected="false"><?= Translate::_('people', 'Notifications'); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#topbar_notifications_request" role="tab"
                           aria-selected="false"><?= Translate::_('people', 'Upload requests'); ?></a>
                    </li>
                </ul>
            </div>

            <!--end: Head -->
            <div class="tab-content idb-messages-tab">
                <div class="tab-pane active show" id="topbar_notifications_messages" role="tabpanel">
                    <div class="kt-notification kt-margin-t-10 kt-margin-b-10 kt-scroll" data-scroll="true"
                         data-height="300" data-mobile-height="200">
                        <?php if (!empty($messages)) { ?>
                            <?php foreach ($messages as $message): ?>
                                <?php $messageContent = json_decode($message->messagecontent); ?>
                                <a href="<?= Url::to(
                                    [
                                        '/business2peoplemessages/btpmessages/details',
                                        'id' => $message->id
                                    ]
                                ) ?>" class="kt-notification__item">
                                    <div class="kt-notification__item-icon">
                                        <i class="flaticon2-envelope kt-font-brand <?= ($message->reviewed) ? ''
                                            : 'kt-font-danger' ?>"></i>
                                    </div>
                                    <div class="kt-notification__item-details">
                                        <div class="kt-notification__item-title">
                                            <?= htmlspecialchars(substr($messageContent->subject, 0, 64)) ?>
                                        </div>
                                        <div class="kt-notification__item-time">
                                            <?= htmlspecialchars(
                                                substr($messageContent->message, 0, 64)
                                            ) ?>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php } ?>
                    </div>
                </div>
                <div class="tab-pane" id="topbar_notifications_notifications" role="tabpanel">
                    <div class="kt-notification kt-margin-t-10 kt-margin-b-10 kt-scroll" data-scroll="true"
                         data-height="300" data-mobile-height="200">
                        <?php if (!empty($notifications)) : ?>
                            <?php foreach ($notifications as $index => $notification): ?>
                                <?php $data = json_decode($notification->data); ?>
                                <?php $IconButton = setButtonIcon($notification->type); ?>
                                <?php $button_type = $IconButton[0]; ?>
                                <?php $icon = $IconButton[1]; ?>
                                <a onclick="show('<?= htmlspecialchars(htmlentities($data->title, ENT_QUOTES, "UTF-8")) ?>', '<?= htmlspecialchars(htmlentities($data->body, ENT_QUOTES, "UTF-8")) ?>', '<?= $icon ?>', <?= $notification->id ?>, '<?= $data->type?? 'undefined' ?>')"
                                   class="kt-notification__item"
                                   style="cursor:pointer;"
                                   id="not_button_<?= $index ?>">
                                    <div class="kt-notification__item-icon">
                                        <i class="flaticon2-notification <?= $button_type ?>"></i>
                                    </div>
                                    <div class="kt-notification__item-details">
                                        <div class="kt-notification__item-title">
                                            <?= $data->title ?>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="tab-pane" id="topbar_notifications_request" role="tabpanel">
                    <div class="kt-notification kt-margin-t-10 kt-margin-b-10 kt-scroll" data-scroll="true"
                         data-height="300" data-mobile-height="200">
                        <?php if (!empty($uploadRequests)) : ?>
                            <?php foreach ($uploadRequests as $uploadRequest): ?>

                            <?= Html::a(
                                    '<div class="kt-notification__item-icon">
                                        <i class="flaticon-file kt-font-danger"></i>
                                    </div>
                                    <div class="kt-notification__item-details">
                                        <div class="kt-notification__item-title">
                                            ' . Translate::_('people', 'You are requested for {request}', ['request' => $uploadRequest->name]) .'
                                        </div>
                                    </div>',
                                    ['/idb-storage/upload-request', 'id' => $uploadRequest->id],
                                    [
                                            'style' => 'cursor:pointer;',
                                        'class' => 'kt-notification__item',
                                        'data' => [
                                            'method' => 'post',
                                            'params' =>array_merge(IdbAccountId::parse($uploadRequest->pid), ['_csrf' => Yii::$app->request->csrfToken])
                                        ]
                                    ]
                            ) ?>

                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>


            </div>
        </form>
    </div>
</div>


<script>
    <?php if(!empty($notificationsJS)): ?>
    window.addEventListener('load', check);
    <?php endif; ?>

    function check() {
        let notifications = JSON.parse('<?= json_encode($notificationsJS) ?>');

        if (notifications.red !== undefined) {
            show(notifications.red[0].data.title, notifications.red[0].data.body, 'error');
            let overlay = document.createElement('div');
            overlay.id = 'mandatory-overlay';
            document.getElementsByTagName("body")[0].appendChild(overlay);
        } else if (notifications.amber !== undefined) {
            for (let notification of notifications.amber) {
                console.log(notification);
                if(notification.data.type !== undefined) {
                    show(notification.data.title, notification.data.body, 'warning', notification.id, notification.data.type);
                } else {
                    show(notification.data.title, notification.data.body, 'warning');
                }
            }
        }
    }

    function show(title, body, icon, id, type) {
        if (icon == 'success') {
            swal.fire({
                title: title,
                text: body,
                type: icon,

                confirmButtonText: "OK",
                confirmButtonClass: "btn btn-danger m-btn m-btn--pill m-btn--air m-btn--icon",

                showCancelButton: true,
                cancelButtonText: "Delete",
                cancelButtonClass: "btn btn-secondary m-btn m-btn--pill m-btn--icon delete_url"
            });
        } else if (icon == 'error') {
            swal.fire({
                title: title,
                text: body,
                type: icon,
                backdrop: false,

                confirmButtonText: "OK",
                confirmButtonClass: "btn btn-danger m-btn m-btn--pill m-btn--air m-btn--icon",

                showCancelButton: false,
                showConfirmButton: false,
                cancelButtonText: "Delete",
                cancelButtonClass: "btn btn-secondary m-btn m-btn--pill m-btn--icon delete_url"
            });
        } else {
            if(type !== undefined && type === 'reviewCycle') {
                body = "<p>It's time to decide if you allow business to keep your data for another cycle</p>";
                body += '<a href="/events/review-cycle-allow?id='+ id +'" class="btn btn-success">Allow</a>';
                body += '<a href="/events/review-cycle-remove?id='+ id +'" class="btn btn-danger">Remove</a>';
                swal.fire({
                    title: title,
                    html: body,
                    type: icon,
                    showCloseButton: true,
                    showConfirmButton: false,
                });
            } else {
                swal.fire(title, body, icon)
            }
        }

        if (icon == 'success') {
            var elements = document.getElementsByClassName('delete_url');
            var button = elements[0];

            function click() {
                window.location = "<?= Url::toRoute(
                    ['/notifications/notifications/delete', 'id' => 'variableId']
                ); ?>".replace('variableId', id);
            }

            button.onclick = click;

        }

    }
</script>
