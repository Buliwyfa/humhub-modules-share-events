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

    static function toCalendarItem($entry)
    {
        // wtf? we need this, because returning $entry directly fucks up with
        // modal loading and return getFullCalendarArray fucks up on stream
        // view because start isn't a date.
        $item = $entry->fullCalendarArray;
        $item['start'] = $entry->getStartDateTime();
        $item['end'] = $entry->getEndDateTime();
        return $item;
    }

    public static function onCalendarFindItems($event)
    {
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
            function($entry) { return Events::toCalendarItem($entry); },
            $calendar->all()
        ));
    }

}

