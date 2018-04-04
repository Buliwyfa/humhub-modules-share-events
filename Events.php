<?php

namespace humhub\modules\share_events;

use Yii;

use humhub\modules\user\models\User;

use humhub\modules\calendar\models\CalendarEntry;

use humhub\modules\share\models\ShareEntry;
use humhub\modules\share\widgets\ShareLink;

/**
 *  MentionSpacesEvents
 */
class Events extends \yii\base\Object
{
    public static function onCalendarGetTypes($event)
    {
        $event->addType('share', [
            'title' => 'Shared event',
            'icon' => 'fa-share-alt',
        ]);
    }

    public static function onCalendarFindItems($event)
    {
        // TODO: visibility/publicity of calendar?
        $contentContainer = $event->contentContainer;
        if(!$contentContainer || !$contentContainer->isModuleEnabled('calendar'))
            return;

        $share_entries = ShareEntry::find()
            ->join('join', 'content', 'content.object_id = share_entry.id')
            ->where([
                'content.object_model' => ShareEntry::className(),
                'content.contentcontainer_id' => $contentContainer->contentcontainer_id
            ]);

        $calendar = CalendarEntry::find()
            ->join('join', 'content', 'content.object_id = calendar_entry.id')
            ->innerJoin(['share' => $share_entries], 'share.entry = content.id')
            ->where([
                'content.object_model' => CalendarEntry::className(),
            ]);

        $event->addItems('share', array_map(
            function($entry) { return $entry->getFullCalendarArray(); },
            $calendar->all()
        ));
    }

}

