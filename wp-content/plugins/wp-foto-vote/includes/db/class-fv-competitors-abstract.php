<?php

/**
 * interface FV_Competitor_ABS
 *
 * @property integer $id
 *
 * @property string $name                   255 chars
 *
 * @property string $description            500 chars
 * @property string $full_description       1255 chars
 * @property string $social_description     255 chars
 * @property string $url
 * @property string $url_min
 * @property string $storage
 * @property string $options                500 chars
 * @property integer $image_id
 * @property string $mime_type
 * @property int    $contest_id
 *
 * @property int    $likes
 * @property int    $dislikes
 * @property int    $views
 *
 * @property int    $votes_count
 * @property int    $rating_summary
 * @property int    $votes_count_fail
 * @property float  $votes_average
 *
 * @property int    $status                 1
 * @property string $added_date             #mysql time
 * @property string $upload_info
 * @property string $user_email
 * @property int    $user_id
 * @property string $user_ip
 * @property int    $place
 * @property string $place_caption
 */
interface FV_Competitor_ABS {}