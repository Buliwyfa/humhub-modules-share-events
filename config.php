<?php

return [
    'id' => 'share_events',
    'class' => 'humhub\modules\share_events\Module',
    'namespace' => 'humhub\modules\share_events',

    'events' => [
        [ 'class' => '\humhub\modules\calendar\interfaces\CalendarService',
          'event' => 'getItemTypes',
          'callback' => ['humhub\modules\share_events\Events', 'onCalendarGetTypes'] ],

        [ 'class' => '\humhub\modules\calendar\interfaces\CalendarService',
          'event' => 'findItems',
          'callback' => ['humhub\modules\share_events\Events', 'onCalendarFindItems'] ],
    ],
];

?>

