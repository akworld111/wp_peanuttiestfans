<?php

function fv_new_year_most_voted ($contest_id) {
    $my_db = new FV_DB;
    return apply_filters( FV::PREFIX . 'most_voted_data',
        $my_db->getMostVotedItems( $contest_id, get_option('fotov-leaders-count', 3) )
    );
}