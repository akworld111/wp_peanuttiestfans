<?php
/** @var FV_Competitor $competitor */
defined('ABSPATH') or die("No script kiddies please!");

$votes_total_1_percent = $votes_total ? $votes_total / 100 : 1;
?>

<div class="wrap fv-page">
    <div class="actions mb20">
        <label for="fv-filter-contest"><?php _e('Contest', 'fv') ?>:</label>
        <select name="contest-filter" id="fv-filter-contest">
            <option value=""><?php _e('Filter by Contest', 'fv') ?></option>
            <?php foreach ($contests as $c_id => $c_name): ?>
                <option value="<?php echo $c_id ?>" <?php echo ($selected_contest_id == $c_id) ? 'selected' : '' ?> ><?php echo $c_name ?></option>
            <?php endforeach; ?>
        </select>

        <label for="fv-filter-contest-photo"><?php _e('Photo', 'fv') ?>:</label>
        <select name="contest-filter-photo"
            id="fv-filter-contest-photo" <?php echo (!$selected_contest_id) ? "disabled" : ""; ?>>
            <?php if (!$selected_contest_id) : ?>
                    <option value=""><?php _e('Select contest', 'fv') ?></option>
            <?php else: ?>
                <option value=""><?php _e('Filter by photo', 'fv') ?></option>
                <?php foreach ($photos as $P): ?>
                        <option
                            value="<?php echo $P->id ?>" <?php echo selected($P->id, $selected_photo_id) ?> ><?php echo '#' , $P->id, ' - ' , $P->name , ' [' , $P->votes_count , ' ♥]' ?></option>
                <?php endforeach; ?>
            <?php endif ?>
        </select>
    </div>

    <?php IF ($selected_contest_id && $selected_photo_id): ?>

        <h2>General info</h2>

        <div class="tile_count b-wrap">

            <div class="">
                <div class="tile_stats_count col-md-5">
                    <span class="count_top"><span class="typcn typcn-heart-outline"></span> Votes rows in database</span>
                    <div class="count">
                        <a href="<?php echo add_query_arg(array('contest_id'=>$selected_contest_id, 'photo_id'=>$selected_photo_id), admin_url('admin.php?page=fv-vote-log')); ?>" target="_blank">
                            <?php echo $votes_total; ?>
                        </a>
                    </div>
                    <span class="count_bottom">&nbsp;</span>
                </div>
                <div class="tile_stats_count col-md-5">
                    <span class="count_top"><span class="typcn typcn-heart-outline"></span> Competitor votes</span>
                    <div class="count"><?php echo $competitor->getVotes(); ?></div>
                    <span class="count_bottom">&nbsp;</span>
                </div>

                <div class="tile_stats_count col-md-5">
                    <span class="count_top"><span class="typcn typcn-heart-full-outline"></span> Denied votes
                        <?php fv_get_tooltip_code( __('Too big percent of denied votes can be one of possible cheating indicators.', 'fv') ); ?></span>
                    <div class="count"><?php echo $competitor->votes_count_fail, '(', round($competitor->votes_count_fail/$votes_total_1_percent, 1), '%)'; ?></div>
                    <span class="count_bottom">(duplicate ip, etc)</span>
                </div>
            </div><!-- /.col-sm-4 -->
            <div class="clearfix clear"></div>
        </div>

        <h2>Cheating (fraud) indicators</h2>

        <div class="tile_count b-wrap">

            <div class="row">
                <div class="tile_stats_count col-md-5">
                    <span class="count_top"><span class="typcn typcn-heart-outline"></span> <a href="https://www.tecmint.com/tor-browser-for-anonymous-web-browsing/" target="_blank">TOR votes summary</a>
                        <?php fv_get_tooltip_code( __('Possible TOR browser votes, that often used for change IP.', 'fv') ); ?></span>
                    <div class="count"><?php echo $cheat_params->is_tor_summary, '(', round($cheat_params->is_tor_summary/$votes_total_1_percent, 1), '%)'; ?></div>
                    <span class="count_bottom">of all votes</span>
                </div>

                <div class="tile_stats_count col-md-5">
                    <span class="count_top"><span class="typcn typcn-heart-outline"></span> AVG Fraud Score
                        <?php fv_get_tooltip_code( __('More than about 25-30% is a good cheating indicator.', 'fv') ); ?></span>
                    <div class="count"><?php echo round($cheat_params->score_avg, 1); ?>%</div>
                    <span class="count_bottom">&nbsp;</span>
                </div>

                <div class="tile_stats_count col-md-5">
                    <span class="count_top"><span class="typcn typcn-stopwatch"></span> Night votes (23-8)
                        <?php fv_get_tooltip_code( __('Too intensive night voting is a good cheating indicator.', 'fv') ); ?></span>
                    <div class="count"><?php echo $night_votes_count , '(' , $night_votes_percent ; ?>%)</div>
                    <span class="count_bottom">of total</span>
                </div>

                <div class="tile_stats_count col-md-4">
                    <span class="count_top"><span class="typcn typcn-stopwatch"></span> AVG Votes per day</span>
                    <div class="count"><?php echo round($votes_per_day, 1); ?></div>
                    <span class="count_bottom">&nbsp;</span>
                </div>

                <div class="tile_stats_count col-md-5">
                    <span class="count_top"><span class="typcn typcn-stopwatch"></span> <a href="https://stackoverflow.com/a/6880668" target="_blank">Empty referer count</a>
                        <?php fv_get_tooltip_code( __('Too many voter with `empty referer` is a good cheating indicator, because normally most of users goes from social networks, emails, websites, etc.', 'fv') ); ?></span>
                    <div class="count"><?php echo $empty_refer_count, '(', $empty_refer_percent, '%)'; ?></div>
                    <span class="count_bottom">&nbsp;</span>
                </div>
            </div><!-- /.col-sm-4 -->
            <div class="clearfix clear"></div>
        </div>




        <div class="b-wrap">

            <div class="col-md-5">
                <h4>Top 5 display sizes <?php fv_get_tooltip_code( __('Too many the same sizes is a good cheating indicator.', 'fv') ); ?></h4>
                <ul>
                    <?php foreach ($top_5_screens as $top_5_screen): ?>
                    <li>
                        <span class="typcn typcn-device-desktop"></span>
                        <?php echo $top_5_screen->display_size, ' (' , $top_5_screen->display_size_summary , ' records / ', round($top_5_screen->display_size_summary/$votes_total_1_percent, 1), '%)'; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="col-md-5">
                <h4>Top 5 voting countries <?php fv_get_tooltip_code( __('Too many foreign countries is a good cheating indicator.', 'fv') ); ?></h4>
                <ul>
                    <?php
                    $votes_countries_results_count = count($votes_country_arr);
                    for ($N=0; $N < 5 && $N < $votes_countries_results_count; $N++): ?>
                    <li>
                        <span class="typcn typcn-location-outline"></span>
                        <?php echo $votes_country_arr[$N]->country, ' (' , $votes_country_arr[$N]->country_votes_count , ' records / ', round($votes_country_arr[$N]->country_votes_count/$votes_total_1_percent, 1), '%)'; ?>
                    </li>
                    <?php endfor; ?>
                </ul>
            </div>


            <div class="col-md-5">
                <h4>Top 5 Operating Systems</h4>
                <ul>
                    <?php foreach ($OS_top5 as $OS_top5_row): ?>
                        <li>
                            <span class="typcn typcn-device-desktop"></span>
                            <?php echo $OS_top5_row->os, ' (' , $OS_top5_row->os_votes_count , ' records / ', round($OS_top5_row->os_votes_count/$votes_total_1_percent, 1), '%)'; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>            

            <div class="clearfix clear"></div>
        </div>

    <?php ENDIF; ?>

    <?php IF ($selected_contest_id): ?>
        <?php IF ($votes_total): ?>

            <h2>Voting chart <?php fv_get_tooltip_code( __('Strong bursts of activity can indicate about possible cheating (in case long term voting, more than 10 days).', 'fv') ); ?></h2>

            <div style="background-color:#FFFFFF">
                <div id="chartdiv" style="width:100%; height:600px;">Loading data</div>
            </div>

            <div id="world-map" class="bg-light-blue-gradient" style="height: 700px;">Loading data</div>
        <?php ELSE: ?>
            <h2 style="color: red;"><?php _e('No votes to display!'); ?></h2>
        <?php ENDIF; ?>
    <?php ELSE: ?>
        <h2 style="color: red;"><?php _e('Please select contest!'); ?></h2>
    <?php ENDIF; ?>

    <script>

        FvLib.addHook("doc_ready", function() {
            jQuery("select#fv-filter-contest-photo").on("change", function () {
                var contestFilter = jQuery("select#fv-filter-contest").val();
                var contestFilterPhoto = jQuery("select#fv-filter-contest-photo").val();

                if (contestFilter != "") {
                    document.location.href = fv_add_query_arg("photo_id", contestFilterPhoto,
                                                fv_add_query_arg("contest_id", contestFilter , "<?php echo esc_url($page_url); ?>")
                                            );
                } else {
                    document.location.href = "<?php echo esc_url($page_url); ?>";
                }
            });

            jQuery("select#fv-filter-contest").on("change", function () {
                var contestFilter = jQuery("select#fv-filter-contest").val();

                if (contestFilter != "") {
                    document.location.href = fv_add_query_arg("contest_id", contestFilter , "<?php echo esc_url($page_url); ?>");
                } else {
                    document.location.href = "<?php echo esc_url($page_url); ?>";
                }
            });

        });

        <?php if ($selected_contest_id): ?>

            var chartData = <?php echo json_encode($chart_votes_arr_res) ?>;
            var chart;
            var chartCursor;

            FvLib.addHook('doc_ready', function() {

                //"use strict";
                //jvectormap data
                var visitorsData = {
                    <?php foreach ($votes_country_arr as $country_votes_row) : ?>
                    "<?php echo $country_votes_row->two_letter_country ?>": <?php echo $country_votes_row->country_votes_count ?>,
                    <?php endforeach; ?>

                };
                //World map by jvectormap
                jQuery('#world-map').vectorMap({
                    map: 'world_mill_en',
                    backgroundColor: "transparent",
                    regionStyle: {
                        initial: {
                            fill: '#e4e4e4',
                            "fill-opacity": 1,
                            stroke: 'none',
                            "stroke-width": 0,
                            "stroke-opacity": 1
                        }
                    },
                    series: {
                        regions: [
                            {
                                values: visitorsData,
                                scale: ["#d7e9f3", "#92c1dc"],
                                normalizeFunction: 'polynomial'
                            }
                        ]
                    },
                    onRegionTipShow: function (e, el, code) {
                        if (typeof visitorsData[code] != "undefined")
                            el.html(el.html() + ': ' + visitorsData[code] + ' votes');
                    }
                });

                for ( var i = 0; i < chartData.length; i++ ) {
                    generateChartData(chartData[i], i);
                }

                function generateChartData(row, N) {
                    // Split timestamp into [ Y, M, D, h, m, s ]
                    //var t = "2010-06-09 13:12:01".split(/[- :]/);
                    var t = row.date.split(/[- :]/);

                    // Apply each element to the Date function
                    //var d =
                    chartData[N].date = new Date(t[0], t[1]-1, t[2], t[3], 00, 00);
                }


                //AmCharts.ready(function () {
                    // generate some data first
                    //generateChartData();

                    // SERIAL CHART
                    chart = new AmCharts.AmSerialChart();

                    chart.dataProvider = chartData;
                    chart.categoryField = "date";
                    chart.balloon.bulletSize = 5;

                    // listen for "dataUpdated" event (fired when chart is rendered) and call zoomChart method when it happens
                    chart.addListener("dataUpdated", zoomChart);

                    // AXES
                    // category
                    var categoryAxis = chart.categoryAxis;
                    categoryAxis.parseDates = true; // as our data is date-based, we set parseDates to true
                    categoryAxis.minPeriod = "DD"; // our data is daily, so we set minPeriod to DD
                    categoryAxis.dashLength = 1;
                    categoryAxis.minorGridEnabled = true;
                    categoryAxis.twoLineMode = true;
                    categoryAxis.dateFormats = [{
                        period: 'fff',
                        format: 'JJ:NN:SS'
                    }, {
                        period: 'ss',
                        format: 'JJ:NN:SS'
                    }, {
                        period: 'mm',
                        format: 'JJ:NN'
                    }, {
                        period: 'hh',
                        format: 'JJ:NN'
                    }, {
                        period: 'DD',
                        format: 'DD'
                    }, {
                        period: 'WW',
                        format: 'DD'
                    }, {
                        period: 'MM',
                        format: 'MMM'
                    }, {
                        period: 'YYYY',
                        format: 'YYYY'
                    }];

                    categoryAxis.axisColor = "#DADADA";

                    // value
                    var valueAxis = new AmCharts.ValueAxis();
                    valueAxis.axisAlpha = 0;
                    valueAxis.dashLength = 1;
                    valueAxis.title = "Votes in selected period";
                    chart.addValueAxis(valueAxis);

                    // GRAPH
                    var graph = new AmCharts.AmGraph();
                    graph.id = "g1";
                    graph.title = "red line";
                    graph.valueField = "votes";
                    graph.bullet = "round";
                    graph.bulletBorderColor = "#FFFFFF";
                    graph.bulletBorderThickness = 2;
                    graph.bulletBorderAlpha = 1;
                    graph.lineThickness = 2;
                    graph.lineColor = "#5fb503";
                    graph.negativeLineColor = "#efcc26";
                    graph.hideBulletsCount = 50; // this makes the chart to hide bullets when there are more than 50 series in selection
                    graph.fillAlphas = 0.3; // setting fillAlphas to > 0 value makes it area graph
                    chart.addGraph(graph);

                    // CURSOR
                    chartCursor = new AmCharts.ChartCursor();
                    chartCursor.cursorPosition = "mouse";
                    chartCursor.pan = true; // set it to fals if you want the cursor to work in "select" mode
                    chartCursor.valueLineEnabled = true;
                    chartCursor.valueLineBalloonEnabled = true;
                    chart.addChartCursor(chartCursor);

                    // SCROLLBAR
                    var chartScrollbar = new AmCharts.ChartScrollbar();
                    chartScrollbar.graph = "g1";
                    chartScrollbar.scrollbarHeight = 80;
                    chartScrollbar.backgroundAlpha= 0;
                    chartScrollbar.selectedBackgroundAlpha= 0.1;
                    chartScrollbar.selectedBackgroundColor= "#888888";
                    chartScrollbar.graphFillAlpha= 0;
                    chartScrollbar.graphLineAlpha= 0.5;
                    chartScrollbar.selectedGraphFillAlpha= 0;
                    chartScrollbar.selectedGraphLineAlpha= 1;
                    chartScrollbar.autoGridCount= true;
                    chartScrollbar.color = "#AAAAAA";

                    chart.addChartScrollbar(chartScrollbar);

                    chart.creditsPosition = "bottom-right";

                    // WRITE
                    chart.write("chartdiv");
                //});
        /*
                // generate some random data, quite different range
                function generateChartData() {
                    var firstDate = new Date();
                    firstDate.setDate(firstDate.getDate() - 500);

                    for (var i = 0; i < 500; i++) {
                        // we create date objects here. In your data, you can have date strings
                        // and then set format of your dates using chart.dataDateFormat property,
                        // however when possible, use date objects, as this will speed up chart rendering.
                        var newDate = new Date(firstDate);
                        newDate.setDate(newDate.getDate() + i);

                        var visits = Math.round(Math.random() * 40) - 20;

                        chartData.push({
                            date: newDate,
                            visits: visits
                        });
                    }
                }
        */
                // this method is called when chart is first inited as we listen for "dataUpdated" event
                function zoomChart() {
                    // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
                    chart.zoomToIndexes(chartData.length - 40, chartData.length - 1);
                }

                // changes cursor mode from pan to select
                function setPanSelect() {
                    if (document.getElementById("rb1").checked) {
                        chartCursor.pan = false;
                        chartCursor.zoomable = true;
                    } else {
                        chartCursor.pan = true;
                    }
                    chart.validateNow();
                }
            });
        <?php ENDIF; ?>
    </script>

    <style>
        .mb20 {
            margin-bottom: 20px;
        }
        .bg-light-blue-gradient {
            background: #3c8dbc !important;
            background: -webkit-gradient(linear, left bottom, left top, color-stop(0, #3c8dbc), color-stop(1, #67a8ce)) !important;
            background: -ms-linear-gradient(bottom, #3c8dbc, #67a8ce) !important;
            background: -moz-linear-gradient(center bottom, #3c8dbc 0%, #67a8ce 100%) !important;
            background: -o-linear-gradient(#67a8ce, #3c8dbc) !important;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#67a8ce', endColorstr='#3c8dbc', GradientType=0) !important;
            color: #fff;
        }


        .tile_count {
            margin-bottom: 20px;
            margin-top: 20px
        }

        .tile_count .tile_stats_count {
            border: 1px solid #D9DEE4;
            padding: 10px 10px 0 20px;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            position: relative;
            text-align: center;
        }

        .count_top > .typcn {
            font-size: 18px !important;
        }

        @media (min-width: 992px) {
            .tile_count .tile_stats_count {
                margin-bottom:10px;
                padding-bottom: 10px
            }
        }

        .tile_count .tile_stats_count:before {
            content: "";
            position: absolute;
            left: 0;
            height: 65px;
            border-left: 2px solid #ADB2B5;
            margin-top: 10px
        }

        .tile_count .tile_stats_count .count {
            font-size: 30px;
            line-height: 47px;
            font-weight: 600
        }

        @media (min-width: 768px) {
            .tile_count .tile_stats_count .count {
                font-size:40px
            }
        }

        @media (min-width: 992px) and (max-width: 1100px) {
            .tile_count .tile_stats_count .count {
                font-size:30px
            }
        }

        .tile_count .tile_stats_count span {
            font-size: 12px
        }

        @media (min-width: 768px) {
            .tile_count .tile_stats_count span {
                font-size:13px
            }
        }

        .tile_count .tile_stats_count .count_bottom i {
            width: 12px
        }

    </style>
</div>