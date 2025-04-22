<?php

include_once 'DesignMyNight.php';

/**
 * Plugin Name: Design My Night Custom Widget
 * Description: This plugin allows you to use custom widgets for My night forms
 * Version: 1.1
 * Author: Lewis Sherlock
 */

if (! class_exists('Design_My_Night')) {

	class Design_My_Night {
		private $booking_data;
		private $url;
		private $times = [];

		public function __construct()
		{
			// Hook styles and scripts enqueue function
			add_action('wp_enqueue_scripts', array($this, 'enqueue_custom_assets'));

			// Load booking data
			$this->load_booking_data();

			// Register shortcodes
			add_shortcode('book_your_visit', array($this, 'book_your_visit_form'));
			add_shortcode('private_hire', array($this, 'private_hire_form'));

			$this->times = $this->hoursRange( 28800, 86400, 60 * 30, 'g:ia' );
			$this->times_server = $this->hoursRange( 28800, 86400, 60 * 30, 'H:i' );
		}

		private function load_booking_data() {

			$DesignMyNight = new DesignMyNight();

			$venues = $DesignMyNight->getVenues();

			if ( $venues ) {
				$this->booking_data = json_decode($venues, true);
			} else {
				$this->booking_data = null;
			}
			
			// Dummy JSON data 
			// if (file_exists($json_file_path)) {
			// 	$json_data = file_get_contents($json_file_path);
			// 	$this->booking_data = json_decode($json_data, true);
			// } else {
			// 	$this->booking_data = null;
			// }
		}

		/**
		 * Enqueue custom styles and scripts
		 */
		public function enqueue_custom_assets()
		{
			// Enqueue custom CSS
			wp_enqueue_style('custom-css-forms', plugin_dir_url(__FILE__) . 'custom-css-forms.css', array(), '1.0', 'all');

			// Enqueue jQuery and jQuery UI
			wp_enqueue_script('jquery');
			wp_enqueue_style('jquery-ui-css', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
			wp_enqueue_script('jquery-ui', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js', array('jquery'), null, true);
		}

		/**
		 * Shortcode handler for [book_your_visit]
		 */
		public function book_your_visit_form()
		{
			$form = isset($_GET['form']) ? $_GET['form'] : '';

			ob_start(); // Start output buffering
			if (!isset($_GET['form'])) {

				if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_visit_btn'])) {
					// Collect form data
					$venue_id = sanitize_text_field($_POST['venue_id']);
					$type = sanitize_text_field($_POST['type']);
					$num_people = sanitize_text_field($_POST['num_people']);
					$date = sanitize_text_field($_POST['date']);
					$time = sanitize_text_field($_POST['time']);

					$form_data = [
						'venue_id' => $venue_id,
						'type' => $type,
						'num_people' => $num_people,
						'date' => $date,
						'time' => $time
					];

					// Redirect to the submit confirmation page
					if (!headers_sent()) {
						header("Location: ?form=booking&" . http_build_query($form_data)); // appending the form data to the URL
						exit;
					} else {
						// Fallback to JavaScript for redirection if headers are already sent
						echo '<script type="text/javascript">window.location.href="?form=booking&' . http_build_query($form_data) . '";</script>';
						exit;
					}
				} ?>

				<div class="form-container form-container-visit">
					<style>
						@font-face {
							font-family: 'Gotham';
							src: url('<?php echo plugin_dir_url(__FILE__); ?>/fonts/GothamBold.ttf') format('truetype');
							font-weight: 700;
							font-style: normal;
						}

						@font-face {
							font-family: 'Gotham';
							src: url('<?php echo plugin_dir_url(__FILE__); ?>/fonts/GothamMedium_1.ttf') format('truetype');
							font-weight: 500;
							font-style: normal;
						}
					</style>
					<h2>Book Your Visit</h2>
					<form action="" method="POST" id="form-booking-visit">
						<!-- form fields -->
						<div class="datepicker">
							<div id="DatepickerBookingVisit"></div>
							<input type="hidden" name="date" id="date">
						</div>
						<div class="fields-container fields-container-booking">
							<!-- Venue -->
							<label for="venue"><span>Venue</span>
								<svg width="25px" height="25px" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
									<title>ionicons-v5-n</title>
									<path d="M256,48c-79.5,0-144,61.39-144,137,0,87,96,224.87,131.25,272.49a15.77,15.77,0,0,0,25.5,0C304,409.89,400,272.07,400,185,400,109.39,335.5,48,256,48Z" style="fill:none;stroke:#fff;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px" />
									<circle cx="256" cy="192" r="48" style="fill:none;stroke:#fff;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px" />
								</svg>
							</label>
							
							<select name="venue_id" id="venue_id" required>
								<option disabled selected="selected">Choose Venue</option>
								
								<?php if ($this->booking_data) foreach ($this->booking_data['payload'] as $venue) {
									$store_code = $venue['title'];
									$venue_id = $venue['_id'];
									
									// Render <option> HTML
									?><option value="<?= $venue_id ?>"><?= $store_code ?></option><?php
								} ?>

							</select>
						</div>
						<div class="fields-container fields-container-booking">
							<!-- Occasion -->
							<label for="type">
								<span>Occasion</span>
								<svg fill="#fff" width="25px" height="25px" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
									<path d="M 24.84375 4.0625 L 23.875 4.34375 L 16.1875 6.53125 L 15.25 6.8125 L 15.4375 7.5 L 8.21875 5.40625 L 7.25 5.125 L 6.96875 6.09375 L 4.15625 15.65625 C 3.816406 16.839844 3.609375 18.253906 4 19.625 C 4.316406 20.734375 5.109375 21.800781 6.34375 22.4375 L 5.4375 25.71875 L 2.5625 24.875 L 2 26.8125 L 9.6875 29.03125 L 10.25 27.09375 L 7.375 26.25 L 8.28125 23 C 9.664063 23.113281 10.890625 22.613281 11.75 21.84375 C 12.8125 20.890625 13.410156 19.621094 13.75 18.4375 L 16.21875 10.1875 L 18.25 17.40625 L 18.25 17.4375 C 18.589844 18.621094 19.1875 19.890625 20.25 20.84375 C 21.117188 21.621094 22.328125 22.089844 23.71875 21.96875 L 24.625 25.25 L 21.75 26.09375 L 22.3125 28.03125 L 30 25.8125 L 29.4375 23.875 L 26.5625 24.71875 L 25.625 21.4375 C 26.859375 20.800781 27.652344 19.734375 27.96875 18.625 C 28.359375 17.253906 28.183594 15.839844 27.84375 14.65625 L 25.125 5.03125 Z M 23.5 6.53125 L 24.75 11 L 18.5 11 L 17.71875 8.1875 Z M 8.625 7.625 L 14.375 9.25 L 13.5625 12 L 7.3125 12 Z M 19.0625 13 L 25.3125 13 L 25.9375 15.21875 C 26.207031 16.148438 26.300781 17.253906 26.0625 18.09375 C 25.824219 18.933594 25.371094 19.523438 24.15625 19.875 C 22.941406 20.226563 22.242188 19.957031 21.59375 19.375 C 20.945313 18.792969 20.425781 17.804688 20.15625 16.875 Z M 6.71875 14 L 12.96875 14 L 11.84375 17.875 L 11.8125 17.875 C 11.542969 18.804688 11.054688 19.792969 10.40625 20.375 C 9.757813 20.957031 9.058594 21.226563 7.84375 20.875 C 6.628906 20.523438 6.175781 19.933594 5.9375 19.09375 C 5.699219 18.253906 5.792969 17.148438 6.0625 16.21875 Z" />
								</svg>
							</label>

							<script>
								// ingenius way to set a JS variable straight from PHP. No need to convert etc.
								// The result will be window.venueEventTypes = [THE ECHOED JSON];
								window.venueEventTypes = 
								<?php 
								// to be got with the venue_id
								$event_types = [];
								
								if ($this->booking_data) foreach ($this->booking_data['payload'] as $venue) {
									$event_types[$venue['_id']] = $venue['booking_types'];
								} 
								echo json_encode($event_types);
								?>;

								// okay now we might as well write the events for the selet here.
								// first we might as well right the function to populate the select with options.

								const populateBookingTypes = (venue_id) => {
									const vanueEventTypeSelect = document.getElementById('type');
									const event_types = window.venueEventTypes[venue_id];

									vanueEventTypeSelect.options.length = 0;
									// console.log(event_types);

									const option = document.createElement("option");
									option.setAttribute("disabled", '');
									option.setAttribute("selected", '');
									option.innerHTML = 'Choose Occasion';
									vanueEventTypeSelect.options.add(option);

									for (const key in event_types) {
										// now we will add options to the select element.
										const option = document.createElement("option");
										option.setAttribute("value", event_types[key].id);
										option.innerHTML = event_types[key].name;

										vanueEventTypeSelect.options.add(option);
									}
								}

								document.getElementById('venue_id').addEventListener('change', (e) => {
									// 
									const option = event.target[event.target.selectedIndex];
									const id = option.value;

									populateBookingTypes(id);
								});
							</script>

							<select name="type" id="type" required>
								<option disabled selected="selected">Choose Occasion</option>

								<?php

								// need to loop through the payload and grab the venue.
								// then use JS to switch between selects.
								// so we actually need to loops for venue and booking types.

								if ($this->booking_data && isset($this->booking_data['payload']['venue']['booking_types'])) {
									$booking_types = $this->booking_data['payload']['venue']['booking_types'];

									// Loop through booking types and extract names
									foreach ($booking_types as $booking_type) {
										$occasion_name = esc_html($booking_type['name']); // Use esc_html to prevent XSS
										$occasion_id = esc_html($booking_type['id']); // Use esc_html to prevent XSS
										echo "<option value=\"{$occasion_id}\">{$occasion_name}</option>";
									}
								}

								?>
							</select>
						</div>

						<div class="fields-container fields-container-booking">
							<!-- Guests -->
							<label for="num_people">
								<span>Guests</span>
								<svg width="25px" height="25px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" clip-rule="evenodd" d="M10 6.5C9.17157 6.5 8.5 7.17157 8.5 8C8.5 8.82843 9.17157 9.5 10 9.5C10.8284 9.5 11.5 8.82843 11.5 8C11.5 7.17157 10.8284 6.5 10 6.5ZM7 8C7 6.34315 8.34315 5 10 5C11.6569 5 13 6.34315 13 8C13 9.65685 11.6569 11 10 11C8.34315 11 7 9.65685 7 8Z" fill="#fff" />
									<path fill-rule="evenodd" clip-rule="evenodd" d="M16 6.5C15.4477 6.5 15 6.94772 15 7.5C15 8.05228 15.4477 8.5 16 8.5C16.5523 8.5 17 8.05228 17 7.5C17 6.94772 16.5523 6.5 16 6.5ZM13.5 7.5C13.5 6.11929 14.6193 5 16 5C17.3807 5 18.5 6.11929 18.5 7.5C18.5 8.88071 17.3807 10 16 10C14.6193 10 13.5 8.88071 13.5 7.5Z" fill="#fff" />
									<path fill-rule="evenodd" clip-rule="evenodd" d="M12.6939 12.6375C11.8834 12.2297 10.9679 12 10 12C6.68629 12 4 14.6863 4 18V20H16V18C16 17.6596 15.9716 17.3255 15.9169 17H20V14.75C20 12.6235 18.1515 11 16 11C14.6441 11 13.422 11.6366 12.6939 12.6375ZM13.9407 13.4754C14.5786 14.0315 15.0981 14.7205 15.4558 15.5H18.5V14.75C18.5 13.5628 17.4384 12.5 16 12.5C15.1288 12.5 14.3841 12.8975 13.9407 13.4754ZM10 13.5C7.51472 13.5 5.5 15.5147 5.5 18V18.5H14.5V18C14.5 17.4727 14.4096 16.9681 14.2441 16.4999C13.9244 15.5953 13.3224 14.8219 12.5431 14.2869C11.8199 13.7905 10.945 13.5 10 13.5Z" fill="#fff" />
								</svg>
							</label>
							<input type="number" name="num_people" id="num_people" placeholder="Choose amount" required/>
						</div>

						<div class="fields-container fields-container-booking">
							<label for="time">
								<span>Time</span>
								<svg width="25px" height="25px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M12 7V12L14.5 13.5M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
								</svg>
							</label>
							<select required name="time" id="time" required>
								<option default disabled>Choose Time</option>
								<?php 
										
								foreach ($this->times as $key => $time) {
									?> <option value="<?= $this->times_server[$key] ?>"><?= $time ?></option> <?php
								}
	
								?>
								<!-- <option value="3">10:00</option> -->
							</select>
						</div>
						<button type="submit" name="book_visit_btn" id="book_visit_btn">Book Now</button>
					</form>
					<script type="text/javascript">
						jQuery(function($) {
							// Initialize the datepicker
							$("#DatepickerBookingVisit").datepicker({
								numberOfMonths: 1,
								dateFormat: "yy-mm-dd",
								minDate :new Date(),
								onSelect: function(dateText) {
									// Set the hidden input value when a date is selected
									$("#date").val(dateText);
								}
							});
						});

						document.addEventListener("DOMContentLoaded", function() {
							// Get the current date
							const today = new Date();

							// Format the date as YYYY-MM-DD
							const formattedDate = today.getFullYear() + '-' +
								('0' + (today.getMonth() + 1)).slice(-2) + '-' +
								('0' + today.getDate()).slice(-2);

							// Set the value of the hidden input
							document.getElementById('date').value = formattedDate;
						});
					</script>
				</div>
			
			<?php } else if ($form == 'booking') {
				// Get the parameters from the URL
				$date = $_GET['date'];
				$venue_id = $_GET['venue_id'];
				$type = $_GET['type'];
				$num_people = $_GET['num_people'];
				$time = $_GET['time'];

				// Construct the URL with the parameters
				$this->url = "https://bookings.designmynight.com/book?venue_id=" . urlencode($venue_id) . "&source=patner&date=" . urlencode($date) . "&type=" . urlencode($type) . "&num_people=" . urlencode($num_people) . "&time=" . urlencode($time);
			?>
				<div class="iframe-container">
					<style>
						.site-header,
						.site-footer,
						.entry-header {
							display: none !important;
						}

						.page {
							padding: 0 !important;
							margin: 0 !important;
						}

						.wp-block-columns {
							padding: 0 !important;
						}
					</style>
					<iframe 
						id="form-iframe" 
						src="<?php echo $this->url; ?>" 
						style="overflow:hidden;overflow-x:hidden;overflow-y:hidden;height:100%;width:100%;position:absolute;top:0px;left:0px;right:0px;bottom:0px"
						height="100%"
						width="100%" width="100%" height="100%" frameborder="0">
					</iframe>
				</div>
			<?php

			}
			return ob_get_clean();
		}

		/**
		 * Every 15 Minutes, All Day Long
		 * $range = hoursRange( 0, 86400, 60 * 15 );
		 * 
		 * Every 30 Minutes from 8 AM - 5 PM, using Custom Time Format
		 * $range = hoursRange( 0, 86400, 60 * 30, 'h:i a' );
		 */
		function hoursRange( $lower = 0, $upper = 86400, $step = 3600, $format = '' ) {
			$times = array();
		
			if ( empty( $format ) ) {
				$format = 'g:i a';
			}
		
			foreach ( range( $lower, $upper, $step ) as $increment ) {
				$increment = gmdate( 'H:i', $increment );
		
				list( $hour, $minutes ) = explode( ':', $increment );
		
				$date = new DateTime( $hour . ':' . $minutes );
		
				$times[(string) $increment] = $date->format( $format );
			}
		
			return $times;
		}

		/**
		 * Shortcode handler for [private_hire]
		 */
		public function private_hire_form() {
			$form = isset($_GET['form']) ? $_GET['form'] : '';
			ob_start();
			if (! isset($_GET['form'])) {

				if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['hire_btn'])) {
					// Collect form data
					$venue_id = sanitize_text_field($_POST['venue_id']);
					$event = sanitize_text_field($_POST['event']);
					// $type = sanitize_text_field($_POST['type']);
					$num_people = sanitize_text_field($_POST['num_people']);
					$date = sanitize_text_field($_POST['date']);
					$time = sanitize_text_field($_POST['time']);

					$time_from = sanitize_text_field($_POST['time-from']);
					$time_to = sanitize_text_field($_POST['time-to']);

					if ($venue_id === '5e4bf7a9d4ea511f3f3feb33') {
						$event = '5ea9f055eae53879de6205a7';
					}
					if ($venue_id === '5e4bf7f3d4ea511da4214824') {
						$event = '615ed107d4ebd34d9818e609';
					}
					if ($venue_id === '5e4bf7dbd4ea511eb57b1773') {
						$event = '5ea9f14b5130d47a6a09ca35';
					}

					$form_data = [
						'venue_id' => $venue_id,
						'type' => $event,
						'num_people' => $num_people,
						'date' => $date,
						'time' => $time_from,
						'duration' => $time_to
					];

					// Redirect to the submit confirmation page
					if (!headers_sent()) {
						header("Location: ?form=private_hire&" . http_build_query($form_data)); // appending the form data to the URL
						exit;
					} else {
						// Fallback to JavaScript for redirection if headers are already sent
						echo '<script type="text/javascript">window.location.href="?form=private_hire&' . http_build_query($form_data) . '";</script>';
						exit;
					}
				}
			?>
				<div class="form-container form-container-hire">
					<h2>Private Hire</h2>
					<form action="" method="POST" id="form-private-hire">
						<!-- form fields -->
						<!-- Datepicker field -->
						<div class="datepicker">
							<div id="DatepickerPrivateHire"></div>
							<input type="hidden" name="date" id="date_private">
						</div>
						<div class="fields-container fields-container-hire">
							<!-- Venue -->
							<label for="venue">
								<span>Venue</span>
								<svg width="25px" height="25px" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
									<title>ionicons-v5-n</title>
									<path d="M256,48c-79.5,0-144,61.39-144,137,0,87,96,224.87,131.25,272.49a15.77,15.77,0,0,0,25.5,0C304,409.89,400,272.07,400,185,400,109.39,335.5,48,256,48Z" style="fill:none;stroke:#fff;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px" />
									<circle cx="256" cy="192" r="48" style="fill:none;stroke:#fff;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px" />
								</svg>
							</label>
							<select name="venue_id" required>
								<option disabled selected>Choose Venue</option>
								
								<?php if ($this->booking_data) foreach ($this->booking_data['payload'] as $venue) {
									$store_code = $venue['title'];
									$venue_id = $venue['_id'];
									
									// Render <option> HTML
									?><option value="<?= $venue_id ?>"><?= $store_code ?></option><?php
								} ?>

							</select>
						</div>
						<div class="fields-container fields-container-hire">
							<!-- Guests -->
							<label for="guests">
								<span>Guests</span>
								<svg width="25px" height="25px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" clip-rule="evenodd" d="M10 6.5C9.17157 6.5 8.5 7.17157 8.5 8C8.5 8.82843 9.17157 9.5 10 9.5C10.8284 9.5 11.5 8.82843 11.5 8C11.5 7.17157 10.8284 6.5 10 6.5ZM7 8C7 6.34315 8.34315 5 10 5C11.6569 5 13 6.34315 13 8C13 9.65685 11.6569 11 10 11C8.34315 11 7 9.65685 7 8Z" fill="#fff" />
									<path fill-rule="evenodd" clip-rule="evenodd" d="M16 6.5C15.4477 6.5 15 6.94772 15 7.5C15 8.05228 15.4477 8.5 16 8.5C16.5523 8.5 17 8.05228 17 7.5C17 6.94772 16.5523 6.5 16 6.5ZM13.5 7.5C13.5 6.11929 14.6193 5 16 5C17.3807 5 18.5 6.11929 18.5 7.5C18.5 8.88071 17.3807 10 16 10C14.6193 10 13.5 8.88071 13.5 7.5Z" fill="#fff" />
									<path fill-rule="evenodd" clip-rule="evenodd" d="M12.6939 12.6375C11.8834 12.2297 10.9679 12 10 12C6.68629 12 4 14.6863 4 18V20H16V18C16 17.6596 15.9716 17.3255 15.9169 17H20V14.75C20 12.6235 18.1515 11 16 11C14.6441 11 13.422 11.6366 12.6939 12.6375ZM13.9407 13.4754C14.5786 14.0315 15.0981 14.7205 15.4558 15.5H18.5V14.75C18.5 13.5628 17.4384 12.5 16 12.5C15.1288 12.5 14.3841 12.8975 13.9407 13.4754ZM10 13.5C7.51472 13.5 5.5 15.5147 5.5 18V18.5H14.5V18C14.5 17.4727 14.4096 16.9681 14.2441 16.4999C13.9244 15.5953 13.3224 14.8219 12.5431 14.2869C11.8199 13.7905 10.945 13.5 10 13.5Z" fill="#fff" />
								</svg>
							</label>
							<input type="number" name="num_people" id="num_people" placeholder="Choose amount" required/>
						</div>

						<div class="fields-container fields-container-hire">
							<label for="time-from">
								<span>From</span>
								<svg width="25px" height="25px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M12 7V12L14.5 13.5M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
								</svg>
							</label>
							<select required name="time-from" required>
								<option default disabled>Choose Time</option>
								<?php 
										
								foreach ($this->times as $key => $time) {
									?> <option value="<?= $this->times_server[$key] ?>"><?= $time ?></option> <?php
								}
	
								?>
							</select>
						</div>
						<div class="fields-container fields-container-hire">
							<label for="time-to">
								<span>To</span>
								<svg width="25px" height="25px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M12 7V12L14.5 13.5M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
								</svg>
							</label>
							<select required name="time-to" required>
								<option default disabled>Choose Time</option>
								<?php 
										
								foreach ($this->times as $key => $time) {
									?> <option value="<?= $this->times_server[$key] ?>"><?= $time ?></option> <?php
								}
	
								?>
							</select>
						</div>
						<button type="submit" name="hire_btn" id="hire_btn">Enquire Now</button>
						<script type="text/javascript">
							jQuery(function($) {
								// Initialize the datepicker
								$("#DatepickerPrivateHire").datepicker({
									numberOfMonths: 1,
									dateFormat: "yy-mm-dd",
									minDate:new Date(),
									onSelect: function(dateText) {
										// Set the hidden input value when a date is selected
										$("#date_private").val(dateText);
									}
								});
							});
						</script>
					</form>
				</div>
			<?php
			} else if ($form == 'private_hire') {
				// Get the parameters from the URL
				$date = $_GET['date'];
				$venue_id = $_GET['venue_id'];
				$type = $_GET['type'];
				//$event = $_GET['event'];
				$num_people = $_GET['num_people'];
				$time = $_GET['time'];

				$time_from = $_GET['time'];
				$time_to = 'number:' . round(abs(strtotime($_GET['duration']) - strtotime($_GET['time'])) / 60,2);

				// Construct the URL with the parameters
				$this->url = "https://bookings.designmynight.com/book?venue_id=" . urlencode($venue_id) 
				. "&source=patner&date=" . urlencode($date) 
				. "&type=" . urlencode($type) 
				. "&num_people=" . urlencode($num_people) 
				. "&time=" . urlencode($time_from)
				. "&duration=" . urlencode($time_to);

			?>
				<div class="iframe-container">
					<iframe id="form-iframe" src="<?php echo $this->url; ?>" style="overflow:hidden;overflow-x:hidden;overflow-y:hidden;height:100%;width:100%;position:absolute;top:0px;left:0px;right:0px;bottom:0px" height="100%" width="100%" width="100%" height="100%" frameborder="0">
					</iframe>
				</div>
			<?php

			}
			return ob_get_clean();
		}
	}

	$ls = new Design_My_Night();
}