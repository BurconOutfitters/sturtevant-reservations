<?php
/*
    iCal Import Addon
*/
require_once dirname( __FILE__ ) . '/sc-res-base-addon.php';

if( !class_exists( 'DEXBCCF_iCalImport' ) )
{
    class DEXBCCF_iCalImport extends DEXBCCF_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-iCalImport-20180619";
		protected $name = "iCal Automatic Import";
		protected $description;

		public function get_addon_form_settings( $form_id )
		{
			global $wpdb;
			// Insertion in database
			if(
				isset( $_REQUEST[ 'DEXBCCF_iCalImport_id' ] )
			)
			{
			    $wpdb->delete( $wpdb->prefix.$this->form_table, array( 'formid' => $form_id ), array( '%d' ) );
				$wpdb->insert(
								$wpdb->prefix.$this->form_table,
								array(
									'formid' => $form_id,

									'ical_enable'	 => $_REQUEST["ical_enable"],

                                    'icalurl'	 => $_REQUEST["icalurl"],

								),
								array( '%d', '%d', '%s' )
							);
                $this->iCalImport($form_id);
			}

			$rows = $wpdb->get_results(
						$wpdb->prepare( "SELECT * FROM ".$wpdb->prefix.$this->form_table." WHERE formid=%d", $form_id )
					);
			if (!count($rows))
			{
			    $row["ical_enable"] = 0;
			    $row["icalurl"] = '';
			} else {
			    $row["ical_enable"] = $rows[0]->ical_enable;
			    $row["icalurl"] = $rows[0]->icalurl;
			}

			?>
			<div id="metabox_basic_settings" class="postbox" >
				<h3 class='hndle' style="padding:5px;"><span><?php print $this->name; ?></span></h3>
				<div class="inside">
				   <input type="hidden" name="DEXBCCF_iCalImport_id" value="1" />
                     Enable iCal import for this calendar? :<br />
                        <?php $option = $row['ical_enable']; ?>
                        <select name="ical_enable" id="ical_enable" onchange="abcical_display_option(this)">
                          <option value="0"<?php if ($option != '1') echo ' selected'; ?>>No</option>
                          <option value="1"<?php if ($option == '1') echo ' selected'; ?>>Yes</option>
                         </select><br />

                   <div id="icalimportcontent_yes" <?php if ($option != '1') echo ' style="display:none"'; ?>>
                         <br />
                         <strong>iCal URL (should start with http:// or https://):</strong><br />
                         <input type="text" name="icalurl" size="70" value="<?php $text = esc_attr($row['icalurl']); echo $text; ?>" /><br />
                   </div>
                   <script type="text/javascript">
                       function abcical_display_option(item)
                       {
                           if (item.selectedIndex == 1)
                               document.getElementById("icalimportcontent_yes").style.display = '';
                           else
                               document.getElementById("icalimportcontent_yes").style.display = 'none';
                       }
                   </script>
				</div>
			</div>
			<?php
		} // end get_addon_form_settings



		/************************ ADDON CODE *****************************/

        /************************ ATTRIBUTES *****************************/

        private $form_table = 'dexbccf_form_icalimport';
        private $_inserted = false;

        /************************ CONSTRUCT *****************************/

        function __construct()
        {
			$this->description = __("The add-on adds support for importing iCal files from external websites/services", 'bccf' );
            // Check if the plugin is active
			if( !$this->addon_is_active() ) return;

			// aqui un action de init o lo que sea para el cron
            if ( ! wp_next_scheduled( 'DEXBCCF_ical_import_hook' ) ) {
                wp_schedule_event( time(), 'hourly', 'DEXBCCF_ical_import_hook' );
            }

            add_action( 'DEXBCCF_ical_import_hook', array( &$this, 'iCalImport' ) );

            $this->update_database();

        } // End __construct



        /************************ PRIVATE METHODS *****************************/

		/**
         * Create the database tables
         */
        protected function update_database()
		{
			global $wpdb;

			$charset_collate = $wpdb->get_charset_collate();
			$sql = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix.$this->form_table." (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					formid INT NOT NULL,
                    ical_enable INT NOT NULL,
                    icalurl TEXT,
					UNIQUE KEY id (id)
				) $charset_collate;";

			$wpdb->query($sql);

		} // end update_database


		/**
         * Create the database tables
         */
        protected function iCalFetch($url)
		{
            $ical = new CP_ICal_reader();
            $ical->initURL($url);

            $tz = date_default_timezone_get();
            date_default_timezone_set( 'UTC' );
            if (isset($ical->cal["VCALENDAR"]["X-WR-TIMEZONE"]) && $ical->cal["VCALENDAR"]["X-WR-TIMEZONE"] != '')
                $offset = timezone_offset_get( timezone_open( $ical->cal["VCALENDAR"]["X-WR-TIMEZONE"] ), new DateTime() );
            else
                $offset = 0;
            date_default_timezone_set( $tz );

            $events = $ical->events();
            $processed_events = array();
            foreach ($events as $event)
                if ( strlen(@$event["DTEND"]) > 1 && strlen(@$event["DTSTART"]) > 1 && !strpos($event['UID'], "@".$_SERVER["SERVER_NAME"]) )
                {
                     $dates = $event['DTSTART'];//get date from ical
                     $dates = str_replace('T', '', $dates);//remove T
                     $dates = str_replace('Z', '', $dates);//remove Z

                     $datee = $event['DTEND'];//get date from ical
                     $datee = str_replace('T', '', $datee);//remove T
                     $datee = str_replace('Z', '', $datee);//remove Z

                     $processed_events[] = array(
                                                  "dates" => date('Y-m-d', strtotime($dates ." ".($offset>0?'+':'').$offset." seconds")),
                                                  "datee" =>  date('Y-m-d', strtotime($datee ." ".($offset>0?'+':'').$offset." seconds")),
                                                  "summary" => $event['SUMMARY'],
                                                  "description" => $event['DESCRIPTION'],
                                                  "UID" => $event['UID']
                                                  );
                }
            return $processed_events;
        }


		/**
         * Create the database tables
         */
        protected function iCalImport($calId = 0)
		{
			global $wpdb;

            $rows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix.$this->form_table." WHERE ical_enable=1".($calId?' AND formid='.intval($calId):'') );
            foreach ($rows as $item)
            {
                try
                {
                    $events = $this->iCalFetch( $item->icalurl );
                }
                catch (Exception $e)
                {
                    echo 'Caught exception: ',  $e->getMessage(), "\n";
                    $events = array();
                }
                // aqui ver cual de los $events hay que poner en la database
                foreach ($events as $event)
                {
                    $check = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".DEX_BCCF_CALENDARS_TABLE_NAME." WHERE reservation_calendar_id=%d AND datatime_s=%s AND datatime_e=%s AND title=%s", $item->formid, $event["dates"], $event["datee"], $event["summary"] ) );
                    if (!count($check))
                    {
                        $wpdb->insert( DEX_BCCF_TABLE_NAME, array( 'calendar' => $item->formid,
                                                                            'time' => current_time('mysql'),
                                                                            'booked_time_s' => $event["dates"],
                                                                            'booked_time_unformatted_s' => $event["dates"],
                                                                            'booked_time_e' => $event["datee"],
                                                                            'booked_time_unformatted_e' => $event["datee"],
                                                                            'name' => $event["summary"],
                                                                            'email' => "",
                                                                            'phone' => "",
                                                                            'notifyto' => "",
                                                                            'question' => $event["summary"]."\n\n".$event["description"],
                                                                            'buffered_date' => serialize(array())
                                                                             ) );
                        $item_number = $wpdb->insert_id;
                        $rows_affected = $wpdb->insert( DEX_BCCF_CALENDARS_TABLE_NAME, array( 'reservation_calendar_id' => $item->formid,
                                                                            'datatime_s' => $event["dates"],
                                                                            'datatime_e' => $event["datee"],
                                                                            'title' => $event["summary"],
                                                                            'description' => str_replace("\n","<br />", $event["summary"]."\n\n".$event["description"]),
                                                                            'viadmin' => 2,
                                                                            'color' => TDE_BCCFCALENDAR_DEFAULT_COLOR,
                                                                            'reference' => intval($item_number)
                                                                             ) );
                    }

                }
            }


		} // end iCalImport


		/************************ PUBLIC METHODS  *****************************/


		/**
		 * mark the item as paid
		 */
		private function _log($adarray = array())
		{
			$h = fopen( __DIR__.'/logs.txt', 'a' );
			$log = "";
			foreach( $_REQUEST as $KEY => $VAL )
			{
				$log .= $KEY.": ".$VAL."\n";
			}
			foreach( $adarray as $KEY => $VAL )
			{
				$log .= $KEY.": ".$VAL."\n";
			}
			$log .= "================================================\n";
			fwrite( $h, $log );
			fclose( $h );
		}



    } // End Class

    // Main add-on code
    $DEXBCCF_iCalImport_obj = new DEXBCCF_iCalImport();

	// Add addon object to the objects list
	global $dexbccf_addons_objs_list;
	$dexbccf_addons_objs_list[ $DEXBCCF_iCalImport_obj->get_addon_id() ] = $DEXBCCF_iCalImport_obj;
}


if( !class_exists( 'CP_ICal_reader' ) )
{

    /**
     * This PHP class should only read an iCal file (*.ics), parse it and return an
     * array with its content.
     *
     * PHP Version 5
     *
     * @category Parser
     * @package  ics-parser
     * @author   Martin Thoma <info@martin-thoma.de>
     * @license  http://www.opensource.org/licenses/mit-license.php MIT License
     * @link     https://github.com/MartinThoma/ics-parser/
     * @example  $ical = new ical('MyCal.ics'); print_r($ical->events());
     * @version  1.0.2
     */

    /**
     * This is the ICal class
     *
     * @param {string} filename The name of the file which should be parsed
     * @constructor
     */
    class CP_ICal_reader
    {
        /* How many ToDos are in this iCal? */
        public /** @type {int} */ $todo_count = 0;

        /* How many events are in this iCal? */
        public /** @type {int} */ $event_count = 0;

        /* How many freebusy are in this iCal? */
        public /** @type {int} */ $freebusy_count = 0;

        /* The parsed calendar */
        public /** @type {Array} */ $cal;

        /* Which keyword has been added to cal at last? */
        private /** @type {string} */ $last_keyword;

        /* The value in years to use for indefinite, recurring events */
        public /** @type {int} */ $default_span = 2;

        /**
         * Creates the iCal Object
         *
         * @param {mixed} $filename The path to the iCal-file or an array of lines from an iCal file
         *
         * @return Object The iCal Object
         */
        public function __construct($filename=false)
        {
            if (!$filename) {
                return false;
            }

            if (is_array($filename)) {
                $lines = $filename;
            } else {
                $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            }

            return $this->initLines($lines);
        }


        /**
         * Initializes lines from a URL
         *
         * @url {string} $url The url of the ical file to download and initialize.  Unless you know what you're doing, it should begin with "http://"
         *
         * @return Object The iCal Object
         */
        public function initURL($url)
        {
            //$contents = file_get_contents($url);

            $ch = curl_init($url);
            curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 0);
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
            $contents = curl_exec( $ch );
            @curl_close ($ch);

            $lines = explode("\n", $contents);

            return $this->initLines($lines);
        }


        /**
         * Initializes lines from file
         *
         * @param {array} $lines The lines to initialize
         *
         * @return Object The iCal Object
         */
        public function initLines($lines)
        {
            if (stristr($lines[0], 'BEGIN:VCALENDAR') === false) {
                return false;
            } else {
                foreach ($lines as $line) {
                    $line = rtrim($line); // Trim trailing whitespace
                    $add  = $this->keyValueFromString($line);

                    if ($add === false) {
                        $this->addCalendarComponentWithKeyAndValue($component, false, $line);
                        continue;
                    }

                    $keyword = $add[0];
                    $values = $add[1]; // Could be an array containing multiple values

                    if (!is_array($values)) {
                        if (!empty($values)) {
                            $values = array($values); // Make an array as not already
                            $blank_array = array(); // Empty placeholder array
                            array_push($values, $blank_array);
                        } else {
                            $values = array(); // Use blank array to ignore this line
                        }
                    } else if(empty($values[0])) {
                        $values = array(); // Use blank array to ignore this line
                    }

                    $values = array_reverse($values); // Reverse so that our array of properties is processed first

                    foreach ($values as $value) {
                        switch ($line) {
                            // http://www.kanzaki.com/docs/ical/vtodo.html
                            case 'BEGIN:VTODO':
                                $this->todo_count++;
                                $component = 'VTODO';
                                break;

                            // http://www.kanzaki.com/docs/ical/vevent.html
                            case 'BEGIN:VEVENT':
                                if (!is_array($value)) {
                                    $this->event_count++;
                                }
                                $component = 'VEVENT';
                                break;

                            // http://www.kanzaki.com/docs/ical/vfreebusy.html
                            case 'BEGIN:VFREEBUSY':
                                $this->freebusy_count++;
                                $component = 'VFREEBUSY';
                                break;

                            // All other special strings
                            case 'BEGIN:VCALENDAR':
                            case 'BEGIN:DAYLIGHT':
                                // http://www.kanzaki.com/docs/ical/vtimezone.html
                            case 'BEGIN:VTIMEZONE':
                            case 'BEGIN:STANDARD':
                            case 'BEGIN:VALARM':
                                $component = $value;
                                break;
                            case 'END:VALARM':
                            case 'END:VTODO': // End special text - goto VCALENDAR key
                            case 'END:VEVENT':
                            case 'END:VFREEBUSY':
                            case 'END:VCALENDAR':
                            case 'END:DAYLIGHT':
                            case 'END:VTIMEZONE':
                            case 'END:STANDARD':
                                $component = 'VCALENDAR';
                                break;
                            default:
                                $this->addCalendarComponentWithKeyAndValue($component, $keyword, $value);
                                break;
                        }
                    }
                }
                $this->process_recurrences();
                return $this->cal;
            }
        }

        /**
         * Add to $this->ical array one value and key.
         *
         * @param {string} $component This could be VTODO, VEVENT, VCALENDAR, ...
         * @param {string} $keyword   The keyword, for example DTSTART
         * @param {string} $value     The value, for example 20110105T090000Z
         *
         * @return {None}
         */
        public function addCalendarComponentWithKeyAndValue($component, $keyword, $value)
        {
            if ($keyword == false) {
                $keyword = $this->last_keyword;
            }

            switch ($component) {
                case 'VTODO':
                    $this->cal[$component][$this->todo_count - 1][$keyword] = $value;
                    break;
                case 'VEVENT':
                    if (!isset($this->cal[$component][$this->event_count - 1][$keyword . '_array'])) {
                        $this->cal[$component][$this->event_count - 1][$keyword . '_array'] = array(); // Create array()
                    }

                    if (is_array($value)) {
                        array_push($this->cal[$component][$this->event_count - 1][$keyword . '_array'], $value); // Add array of properties to the end
                    } else {
                        if (!isset($this->cal[$component][$this->event_count - 1][$keyword])) {
                            $this->cal[$component][$this->event_count - 1][$keyword] = $value;
                        }

                        $this->cal[$component][$this->event_count - 1][$keyword . '_array'][] = $value;

                        // Glue back together for multi-line content
                        if ($this->cal[$component][$this->event_count - 1][$keyword] != $value) {
                            $ord = (isset($value[0])) ? ord($value[0]) : NULL; // First char

                            if(in_array($ord, array(9, 32))){ // Is space or tab?
                                $value = substr($value, 1); // Only trim the first character
                            }

                            if(is_array($this->cal[$component][$this->event_count - 1][$keyword . '_array'][1])){ // Account for multiple definitions of current keyword (e.g. ATTENDEE)
                                $this->cal[$component][$this->event_count - 1][$keyword] .= ';' . $value; // Concat value *with separator* as content spans multiple lines
                            } else {
                                $this->cal[$component][$this->event_count - 1][$keyword] .= $value; // Concat value as content spans multiple lines
                            }
                        }
                    }
                    break;
                case 'VFREEBUSY':
                    $this->cal[$component][$this->freebusy_count - 1][$keyword] = $value;
                    break;
                default:
                    $this->cal[$component][$keyword] = $value;
                    break;
            }
            $this->last_keyword = $keyword;
        }

        /**
         * Get a key-value pair of a string.
         *
         * @param {string} $text which is like "VCALENDAR:Begin" or "LOCATION:"
         *
         * @return {array} array("VCALENDAR", "Begin")
         */
        public function keyValueFromString($text)
        {
            // Match colon separator outside of quoted substrings
            // Fallback to nearest semicolon outside of quoted substrings, if colon cannot be found
            // Do not try and match within the value paired with the keyword
            preg_match('/(.*?)(?::(?=(?:[^"]*"[^"]*")*[^"]*$)|;(?=[^:]*$))([\w\W]*)/', $text, $matches);

            if (count($matches) == 0) {
                return false;
            }

            if (preg_match('/^([A-Z-]+)([;][\w\W]*)?$/', $matches[1])) {
                $matches = array_splice($matches, 1, 2); // Remove first match and re-align ordering

                // Process properties
                if (preg_match('/([A-Z-]+)[;]([\w\W]*)/', $matches[0], $properties)) {
                    array_shift($properties); // Remove first match
                    $matches[0] = $properties[0]; // Fix to ignore everything in keyword after a ; (e.g. Language, TZID, etc.)
                    array_shift($properties); // Repeat removing first match

                    $formatted = array();
                    foreach ($properties as $property) {
                        preg_match_all('~[^\r\n";]+(?:"[^"\\\]*(?:\\\.[^"\\\]*)*"[^\r\n";]*)*~', $property, $attributes); // Match semicolon separator outside of quoted substrings
                        $attributes = (sizeof($attributes) == 0) ? array($property) : reset($attributes); // Remove multi-dimensional array and use the first key

                        foreach ($attributes as $attribute) {
                            preg_match_all('~[^\r\n"=]+(?:"[^"\\\]*(?:\\\.[^"\\\]*)*"[^\r\n"=]*)*~', $attribute, $values); // Match equals sign separator outside of quoted substrings
                            $value = (sizeof($values) == 0) ? NULL : reset($values); // Remove multi-dimensional array and use the first key

                            if (is_array($value) && isset($value[1])) {
                                $formatted[$value[0]] = trim($value[1], '"'); // Remove double quotes from beginning and end only
                            }
                        }
                    }

                    $properties[0] = $formatted; // Assign the keyword property information

                    array_unshift($properties, $matches[1]); // Add match to beginning of array
                    $matches[1] = $properties;
                }

                return $matches;
            } else {
                return false; // Ignore this match
            }
        }

        /**
         * Return Unix timestamp from iCal date time format
         *
         * @param {string} $icalDate A Date in the format YYYYMMDD[T]HHMMSS[Z] or
         *                           YYYYMMDD[T]HHMMSS
         *
         * @return {int}
         */
        public function iCalDateToUnixTimestamp($icalDate)
        {
            $icalDate = str_replace('T', '', $icalDate);
            $icalDate = str_replace('Z', '', $icalDate);

            $pattern  = '/([0-9]{4})';   // 1: YYYY
            $pattern .= '([0-9]{2})';    // 2: MM
            $pattern .= '([0-9]{2})';    // 3: DD
            $pattern .= '([0-9]{0,2})';  // 4: HH
            $pattern .= '([0-9]{0,2})';  // 5: MM
            $pattern .= '([0-9]{0,2})/'; // 6: SS
            preg_match($pattern, $icalDate, $date);

            // Unix timestamp can't represent dates before 1970
            if ($date[1] <= 1970) {
                return false;
            }
            // Unix timestamps after 03:14:07 UTC 2038-01-19 might cause an overflow
            // if 32 bit integers are used.
            $timestamp = mktime((int)$date[4], (int)$date[5], (int)$date[6], (int)$date[2], (int)$date[3], (int)$date[1]);
            return $timestamp;
        }

        /**
         * Processes recurrences
         *
         * @author John Grogg <john.grogg@gmail.com>
         * @return {array}
         */
        public function process_recurrences()
        {
            $array = $this->cal;
            $events = $array['VEVENT'];
            if (empty($events))
                return false;
            foreach ($array['VEVENT'] as $anEvent) {
                if (isset($anEvent['RRULE']) && $anEvent['RRULE'] != '') {
                    // Recurring event, parse RRULE and add appropriate duplicate events
                    $rrules = array();
                    $rrule_strings = explode(';', $anEvent['RRULE']);
                    foreach ($rrule_strings as $s) {
                        list($k, $v) = explode('=', $s);
                        $rrules[$k] = $v;
                    }
                    // Get frequency
                    $frequency = $rrules['FREQ'];
                    // Get Start timestamp
                    $start_timestamp = $this->iCalDateToUnixTimestamp($anEvent['DTSTART']);
                    $end_timestamp = $this->iCalDateToUnixTimestamp($anEvent['DTEND']);
                    $event_timestmap_offset = $end_timestamp - $start_timestamp;
                    // Get Interval
                    $interval = (isset($rrules['INTERVAL']) && $rrules['INTERVAL'] != '') ? $rrules['INTERVAL'] : 1;

                    if (in_array($frequency, array('MONTHLY', 'YEARLY')) && isset($rrules['BYDAY']) && $rrules['BYDAY'] != '') {
                        // Deal with BYDAY
                        $day_number = intval($rrules['BYDAY']);
                        if (empty($day_number)) { // Returns 0 when no number defined in BYDAY
                            if (!isset($rrules['BYSETPOS'])) {
                                $day_number = 1; // Set first as default
                            } else if (is_numeric($rrules['BYSETPOS'])) {
                                $day_number = $rrules['BYSETPOS'];
                            }
                        }
                        $day_number = ($day_number == -1) ? 6 : $day_number; // Override for our custom key (6 => 'last')
                        $week_day = substr($rrules['BYDAY'], -2);
                        $day_ordinals = array(1 => 'first', 2 => 'second', 3 => 'third', 4 => 'fourth', 5 => 'fifth', 6 => 'last');
                        $weekdays = array('SU' => 'sunday', 'MO' => 'monday', 'TU' => 'tuesday', 'WE' => 'wednesday', 'TH' => 'thursday', 'FR' => 'friday', 'SA' => 'saturday');
                    }

                    $until_default = date_create('now');
                    $until_default->modify($this->default_span . ' year');
                    $until_default->setTime(23, 59, 59); // End of the day
                    $until_default = date_format($until_default, 'Ymd\THis');

                    if (isset($rrules['UNTIL'])) {
                        // Get Until
                        $until = $this->iCalDateToUnixTimestamp($rrules['UNTIL']);
                    } else if (isset($rrules['COUNT'])) {
                        $frequency_conversion = array('DAILY' => 'day', 'WEEKLY' => 'week', 'MONTHLY' => 'month', 'YEARLY' => 'year');
                        $count_orig = (is_numeric($rrules['COUNT']) && $rrules['COUNT'] > 1) ? $rrules['COUNT'] : 0;
                        $count = ($count_orig - 1); // Remove one to exclude the occurrence that initialises the rule
                        $count += ($count > 0) ? $count * ($interval - 1) : 0;
                        $offset = "+$count " . $frequency_conversion[$frequency];
                        $until = strtotime($offset, $start_timestamp);

                        if (in_array($frequency, array('MONTHLY', 'YEARLY')) && isset($rrules['BYDAY']) && $rrules['BYDAY'] != '') {
                            $dtstart = date_create($anEvent['DTSTART']);
                            for ($i = 1; $i <= $count; $i++) {
                                $dtstart_clone = clone $dtstart;
                                $dtstart_clone->modify('next ' . $frequency_conversion[$frequency]);
                                $offset = "{$day_ordinals[$day_number]} {$weekdays[$week_day]} of " . $dtstart_clone->format('F Y H:i:01');
                                $dtstart->modify($offset);
                            }

                            // Jumping X months forwards doesn't mean the end date will fall on the same day defined in BYDAY
                            // Use the largest of these to ensure we are going far enough in the future to capture our final end day
                            $until = max($until, $dtstart->format('U'));
                        }

                        unset($offset);
                    } else {
                        $until = $this->iCalDateToUnixTimestamp($until_default);
                    }

                    if(!isset($anEvent['EXDATE_array'])){
                        $anEvent['EXDATE_array'] = array();
                    }

                    // Decide how often to add events and do so
                    switch ($frequency) {
                        case 'DAILY':
                            // Simply add a new event each interval of days until UNTIL is reached
                            $offset = "+$interval day";
                            $recurring_timestamp = strtotime($offset, $start_timestamp);

                            while ($recurring_timestamp <= $until) {
                                // Add event
                                $anEvent['DTSTART'] = date('Ymd\THis', $recurring_timestamp);
                                $anEvent['DTEND'] = date('Ymd\THis', $recurring_timestamp + $event_timestmap_offset);

                                $search_date = $anEvent['DTSTART'];
                                $is_excluded = array_filter($anEvent['EXDATE_array'], function($val) use ($search_date) { return is_string($val) && strpos($search_date, $val) === 0; });

                                if (!$is_excluded) {
                                    $events[] = $anEvent;
                                }

                                // Move forwards
                                $recurring_timestamp = strtotime($offset, $recurring_timestamp);
                            }
                            break;
                        case 'WEEKLY':
                            // Create offset
                            $offset = "+$interval week";
                            // Build list of days of week to add events
                            $weekdays = array('SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA');

                            if (isset($rrules['BYDAY']) && $rrules['BYDAY'] != '') {
                                $bydays = explode(',', $rrules['BYDAY']);
                            } else {
                                $weekTemp = array('SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA');
                                $findDay = $weekTemp[date('w', $start_timestamp)];
                                $bydays = array($findDay);
                            }

                            // Get timestamp of first day of start week
                            $week_recurring_timestamp = (date('w', $start_timestamp) == 0) ? $start_timestamp : strtotime('last Sunday ' . date('H:i:s', $start_timestamp), $start_timestamp);

                            // Step through weeks
                            while ($week_recurring_timestamp <= $until) {
                                // Add events for bydays
                                $day_recurring_timestamp = $week_recurring_timestamp;

                                foreach ($weekdays as $day) {
                                    // Check if day should be added

                                    if (in_array($day, $bydays) && $day_recurring_timestamp > $start_timestamp && $day_recurring_timestamp <= $until) {
                                        // Add event to day
                                        $anEvent['DTSTART'] = date('Ymd\THis', $day_recurring_timestamp);
                                        $anEvent['DTEND'] = date('Ymd\THis', $day_recurring_timestamp + $event_timestmap_offset);

                                        $search_date = $anEvent['DTSTART'];
                                        $is_excluded = array_filter($anEvent['EXDATE_array'], function($val) use ($search_date) { return is_string($val) && strpos($search_date, $val) === 0; });

                                        if (!$is_excluded) {
                                            $events[] = $anEvent;
                                        }
                                    }

                                    // Move forwards a day
                                    $day_recurring_timestamp = strtotime('+1 day', $day_recurring_timestamp);
                                }

                                // Move forwards $interval weeks
                                $week_recurring_timestamp = strtotime($offset, $week_recurring_timestamp);
                            }
                            break;
                        case 'MONTHLY':
                            // Create offset
                            $offset = "+$interval month";
                            $recurring_timestamp = strtotime($offset, $start_timestamp);

                            if (isset($rrules['BYMONTHDAY']) && $rrules['BYMONTHDAY'] != '') {
                                // Deal with BYMONTHDAY
                                $monthdays = explode(',', $rrules['BYMONTHDAY']);

                                while ($recurring_timestamp <= $until) {
                                    foreach ($monthdays as $monthday) {
                                        // Add event
                                        $anEvent['DTSTART'] = date('Ym' . sprintf('%02d', $monthday) . '\THis', $recurring_timestamp);
                                        $anEvent['DTEND'] = date('Ymd\THis', $this->iCalDateToUnixTimestamp($anEvent['DTSTART']) + $event_timestmap_offset);

                                        $search_date = $anEvent['DTSTART'];
                                        $is_excluded = array_filter($anEvent['EXDATE_array'], function($val) use ($search_date) { return is_string($val) && strpos($search_date, $val) === 0; });

                                        if (!$is_excluded) {
                                            $events[] = $anEvent;
                                        }
                                    }

                                    // Move forwards
                                    $recurring_timestamp = strtotime($offset, $recurring_timestamp);
                                }
                            } else if (isset($rrules['BYDAY']) && $rrules['BYDAY'] != '') {
                                $start_time = date('His', $start_timestamp);

                                while ($recurring_timestamp <= $until) {
                                    $event_start_desc = "{$day_ordinals[$day_number]} {$weekdays[$week_day]} of " . date('F Y H:i:s', $recurring_timestamp);
                                    $event_start_timestamp = strtotime($event_start_desc);

                                    if ($event_start_timestamp > $start_timestamp && $event_start_timestamp < $until) {
                                        $anEvent['DTSTART'] = date('Ymd\T', $event_start_timestamp) . $start_time;
                                        $anEvent['DTEND'] = date('Ymd\THis', $this->iCalDateToUnixTimestamp($anEvent['DTSTART']) + $event_timestmap_offset);

                                        $search_date = $anEvent['DTSTART'];
                                        $is_excluded = array_filter($anEvent['EXDATE_array'], function($val) use ($search_date) { return is_string($val) && strpos($search_date, $val) === 0; });

                                        if (!$is_excluded) {
                                            $events[] = $anEvent;
                                        }
                                    }

                                    // Move forwards
                                    $recurring_timestamp = strtotime($offset, $recurring_timestamp);
                                }
                            }
                            break;
                        case 'YEARLY':
                            // Create offset
                            $offset = "+$interval year";
                            $recurring_timestamp = strtotime($offset, $start_timestamp);
                            $month_names = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');

                            // Check if BYDAY rule exists
                            if (isset($rrules['BYDAY']) && $rrules['BYDAY'] != '') {
                                $start_time = date('His', $start_timestamp);

                                while ($recurring_timestamp <= $until) {
                                    $event_start_desc = "{$day_ordinals[$day_number]} {$weekdays[$week_day]} of {$month_names[$rrules['BYMONTH']]} " . date('Y H:i:s', $recurring_timestamp);
                                    $event_start_timestamp = strtotime($event_start_desc);

                                    if ($event_start_timestamp > $start_timestamp && $event_start_timestamp < $until) {
                                        $anEvent['DTSTART'] = date('Ymd\T', $event_start_timestamp) . $start_time;
                                        $anEvent['DTEND'] = date('Ymd\THis', $this->iCalDateToUnixTimestamp($anEvent['DTSTART']) + $event_timestmap_offset);

                                        $search_date = $anEvent['DTSTART'];
                                        $is_excluded = array_filter($anEvent['EXDATE_array'], function($val) use ($search_date) { return is_string($val) && strpos($search_date, $val) === 0; });

                                        if (!$is_excluded) {
                                            $events[] = $anEvent;
                                        }
                                    }

                                    // Move forwards
                                    $recurring_timestamp = strtotime($offset, $recurring_timestamp);
                                }
                            } else {
                                $day = date('d', $start_timestamp);
                                $start_time = date('His', $start_timestamp);

                                // Step through years
                                while ($recurring_timestamp <= $until) {
                                    // Add specific month dates
                                    if (isset($rrules['BYMONTH']) && $rrules['BYMONTH'] != '') {
                                        $event_start_desc = "$day {$month_names[$rrules['BYMONTH']]} " . date('Y H:i:s', $recurring_timestamp);
                                    } else {
                                        $event_start_desc = $day . date('F Y H:i:s', $recurring_timestamp);
                                    }

                                    $event_start_timestamp = strtotime($event_start_desc);

                                    if ($event_start_timestamp > $start_timestamp && $event_start_timestamp < $until) {
                                        $anEvent['DTSTART'] = date('Ymd\T', $event_start_timestamp) . $start_time;
                                        $anEvent['DTEND'] = date('Ymd\THis', $this->iCalDateToUnixTimestamp($anEvent['DTSTART']) + $event_timestmap_offset);

                                        $search_date = $anEvent['DTSTART'];
                                        $is_excluded = array_filter($anEvent['EXDATE_array'], function($val) use ($search_date) { return is_string($val) && strpos($search_date, $val) === 0; });

                                        if (!$is_excluded) {
                                            $events[] = $anEvent;
                                        }
                                    }

                                    // Move forwards
                                    $recurring_timestamp = strtotime($offset, $recurring_timestamp);
                                }
                            }
                            break;

                        $events = (isset($count_orig) && sizeof($events) > $count_orig) ? array_slice($events, 0, $count_orig) : $events; // Ensure we abide by COUNT if defined
                    }
                }
            }
            $this->cal['VEVENT'] = $events;
        }

        /**
         * Returns an array of arrays with all events. Every event is an associative
         * array and each property is an element it.
         *
         * @return {array}
         */
        public function events()
        {
            $array = $this->cal;
            return $array['VEVENT'];
        }

        /**
         * Returns the calendar name
         *
         * @return {calendar name}
         */
        public function calendarName()
        {
            return $this->cal['VCALENDAR']['X-WR-CALNAME'];
        }

        /**
         * Returns the calendar description
         *
         * @return {calendar description}
         */
        public function calendarDescription()
        {
            return $this->cal['VCALENDAR']['X-WR-CALDESC'];
        }

        /**
         * Returns an array of arrays with all free/busy events. Every event is
         * an associative array and each property is an element it.
         *
         * @return {array}
         */
        public function freeBusyEvents()
        {
            $array = $this->cal;
            return $array['VFREEBUSY'];
        }

        /**
         * Returns a boolean value whether thr current calendar has events or not
         *
         * @return {boolean}
         */
        public function hasEvents()
        {
            return (count($this->events()) > 0) ? true : false;
        }

        /**
         * Returns false when the current calendar has no events in range, else the
         * events.
         *
         * Note that this function makes use of a UNIX timestamp. This might be a
         * problem on January the 29th, 2038.
         * See http://en.wikipedia.org/wiki/Unix_time#Representing_the_number
         *
         * @param {boolean} $rangeStart Either true or false
         * @param {boolean} $rangeEnd   Either true or false
         *
         * @return {mixed}
         */
        public function eventsFromRange($rangeStart = false, $rangeEnd = false)
        {
            $events = $this->sortEventsWithOrder($this->events(), SORT_ASC);

            if (!$events) {
                return false;
            }

            $extendedEvents = array();

            if ($rangeStart === false) {
                $rangeStart = new DateTime();
            } else {
                $rangeStart = new DateTime($rangeStart);
            }

            if ($rangeEnd === false or $rangeEnd <= 0) {
                $rangeEnd = new DateTime('2038/01/18');
            } else {
                $rangeEnd = new DateTime($rangeEnd);
            }

            $rangeStart = $rangeStart->format('U');
            $rangeEnd   = $rangeEnd->format('U');

            // Loop through all events by adding two new elements
            foreach ($events as $anEvent) {
                $timestamp = $this->iCalDateToUnixTimestamp($anEvent['DTSTART']);
                if ($timestamp >= $rangeStart && $timestamp <= $rangeEnd) {
                    $extendedEvents[] = $anEvent;
                }
            }

            return $extendedEvents;
        }

        /**
         * Returns a boolean value whether the current calendar has events or not
         *
         * @param {array} $events    An array with events.
         * @param {array} $sortOrder Either SORT_ASC, SORT_DESC, SORT_REGULAR,
         *                           SORT_NUMERIC, SORT_STRING
         *
         * @return {boolean}
         */
        public function sortEventsWithOrder($events, $sortOrder = SORT_ASC)
        {
            $extendedEvents = array();

            // Loop through all events by adding two new elements
            foreach ($events as $anEvent) {
                if (!array_key_exists('UNIX_TIMESTAMP', $anEvent)) {
                    $anEvent['UNIX_TIMESTAMP'] = $this->iCalDateToUnixTimestamp($anEvent['DTSTART']);
                }

                if (!array_key_exists('REAL_DATETIME', $anEvent)) {
                    $anEvent['REAL_DATETIME'] = date('d.m.Y', $anEvent['UNIX_TIMESTAMP']);
                }

                $extendedEvents[] = $anEvent;
            }

            foreach ($extendedEvents as $key => $value) {
                $timestamp[$key] = $value['UNIX_TIMESTAMP'];
            }
            array_multisort($timestamp, $sortOrder, $extendedEvents);

            return $extendedEvents;
        }
    }
}