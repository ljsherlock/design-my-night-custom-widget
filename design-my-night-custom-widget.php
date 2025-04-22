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
		private $times = [ 
			'09:00-11:00',
			'10:00-12:00',
			'11:00-13:00',
			'12:00-14:00',
			'13:00-15:00',
			'14:00-16:00',
			'15:00-17:00',
			'16:00-18:00',
			'17:00-19:00',
			'18:00-20:00',
			'19:00-21:00',
			'20:00-22:00',
			'21:00-23:00'
		];

		public function __construct()
		{
			// Hook styles and scripts enqueue function
			add_action('wp_enqueue_scripts', array($this, 'enqueue_custom_assets'));

			// Load booking data
			$this->load_booking_data();

			// Register shortcodes
			add_shortcode('book_your_visit', array($this, 'book_your_visit_form'));
			add_shortcode('private_hire', array($this, 'private_hire_form'));
		}

		private function load_booking_data()
		{
			// $json_file_path = plugin_dir_path(__FILE__) . 'make-my-night-venue.json';
			// use API here (ljsherlock)

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
							<input type="number" name="num_people" id="num_people" required/>
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
										
								foreach ($this->times as $time) {
									?> <option value="<?= $time ?>"><?= $time ?></option> <?php
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
					<iframe id="form-iframe" src="<?php echo $this->url; ?>" style="overflow:hidden;overflow-x:hidden;overflow-y:hidden;height:100%;width:100%;position:absolute;top:0px;left:0px;right:0px;bottom:0px" height="100%" width="100%" width="100%" height="100%" frameborder="0">
					</iframe>
				</div>
			<?php

			}
			return ob_get_clean();
		}

		/**
		 * Shortcode handler for [private_hire]
		 */
		public function private_hire_form()
		{
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
					// var_dump($date, $time, $venue_id, $event, $num_people);

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
						'time' => $time
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
							<!-- Event -->
							<label for="event">
								<span>Event</span>
								<svg width="25px" height="25px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M13.3057 18.2975L8.23724 19.987C5.47183 20.9088 4.08912 21.3697 3.35924 20.6398C2.62936 19.9099 3.09026 18.5272 4.01207 15.7618L5.70156 10.6933C6.46758 8.39525 6.85059 7.24623 7.75684 7.03229C8.6631 6.81835 9.51953 7.67478 11.2324 9.38764L14.6114 12.7666C16.3242 14.4795 17.1807 15.3359 16.9667 16.2422" stroke="#fff" stroke-width="1.5" stroke-linecap="round" />
									<path d="M12.2351 18.3461C12.2351 18.3461 11.477 16.0649 11.477 14.5552C11.477 13.0454 12.2351 10.7643 12.2351 10.7643M8.06517 19.4833C8.06517 19.4833 7.42484 16.7314 7.307 14.9343C7.11229 11.965 8.06517 7.35254 8.06517 7.35254" stroke="#fff" stroke-width="1.5" stroke-linecap="round" />
									<path d="M14.5093 10.0061L14.6533 9.28614C14.7986 8.55924 15.3224 7.96597 16.0256 7.73155C16.7289 7.49714 17.2526 6.90387 17.398 6.17697L17.542 5.45703" stroke="#fff" stroke-width="1.5" stroke-linecap="round" />
									<path d="M17.5693 13.2533L17.7822 13.3762C18.4393 13.7556 19.2655 13.6719 19.8332 13.1685C20.3473 12.7126 21.0794 12.597 21.709 12.8723L22.0005 12.9997" stroke="#fff" stroke-width="1.5" stroke-linecap="round" />
									<path d="M9.79513 2.77903C9.45764 3.33109 9.54223 4.04247 9.99976 4.5L10.0976 4.59788C10.4908 4.99104 10.6358 5.56862 10.4749 6.10085" stroke="#fff" stroke-width="1.5" stroke-linecap="round" />
									<path d="M6.92761 3.94079C7.13708 3.73132 7.47669 3.73132 7.68616 3.94079C7.89563 4.15026 7.89563 4.48988 7.68616 4.69935C7.47669 4.90882 7.13708 4.90882 6.92761 4.69935C6.71814 4.48988 6.71814 4.15026 6.92761 3.94079Z" fill="#fff" />
									<path d="M12.1571 7.1571C12.3666 6.94763 12.7062 6.94763 12.9157 7.1571C13.1251 7.36657 13.1251 7.70619 12.9157 7.91566C12.7062 8.12512 12.3666 8.12512 12.1571 7.91566C11.9476 7.70619 11.9476 7.36657 12.1571 7.1571Z" fill="#fff" />
									<path d="M17.1571 10.1571C17.3666 9.94763 17.7062 9.94763 17.9157 10.1571C18.1251 10.3666 18.1251 10.7062 17.9157 10.9157C17.7062 11.1251 17.3666 11.1251 17.1571 10.9157C16.9476 10.7062 16.9476 10.3666 17.1571 10.1571Z" fill="#fff" />
									<path d="M19.0582 15.3134C19.2677 15.1039 19.6073 15.1039 19.8168 15.3134C20.0262 15.5228 20.0262 15.8624 19.8168 16.0719C19.6073 16.2814 19.2677 16.2814 19.0582 16.0719C18.8488 15.8624 18.8488 15.5228 19.0582 15.3134Z" fill="#fff" />
									<path d="M19.3615 7.71436C18.6912 8.38463 19.1722 10.328 19.1722 10.328C19.1722 10.328 21.1156 10.809 21.7859 10.1387C22.4958 9.42884 22.0941 8.49708 21.0002 8.5C21.0032 7.40615 20.0714 7.00447 19.3615 7.71436Z" stroke="#fff" stroke-linejoin="round" />
									<path d="M15.1884 3.41748L15.1608 3.51459C15.1305 3.62126 15.1153 3.67459 15.1225 3.72695C15.1296 3.77931 15.1583 3.82476 15.2157 3.91567L15.2679 3.99844C15.4698 4.31836 15.5707 4.47831 15.5019 4.60915C15.4332 4.73998 15.2402 4.75504 14.8544 4.78517L14.7546 4.79296C14.6449 4.80152 14.5901 4.8058 14.5422 4.83099C14.4943 4.85618 14.4587 4.89943 14.3875 4.98592L14.3226 5.06467C14.072 5.36905 13.9467 5.52124 13.8038 5.50167C13.6609 5.4821 13.595 5.30373 13.4632 4.94699L13.4291 4.85469C13.3916 4.75332 13.3729 4.70263 13.3361 4.66584C13.2993 4.62905 13.2486 4.61033 13.1472 4.57287L13.0549 4.53878C12.6982 4.40698 12.5198 4.34108 12.5003 4.19815C12.4807 4.05522 12.6329 3.92992 12.9373 3.67932L13.016 3.61448C13.1025 3.54327 13.1458 3.50767 13.1709 3.45974C13.1961 3.41181 13.2004 3.35699 13.209 3.24735L13.2168 3.14753C13.2469 2.76169 13.262 2.56877 13.3928 2.50001C13.5236 2.43124 13.6836 2.53217 14.0035 2.73403L14.0863 2.78626C14.1772 2.84362 14.2226 2.8723 14.275 2.87947C14.3273 2.88664 14.3807 2.87148 14.4873 2.84117L14.5845 2.81358C14.9598 2.70692 15.1475 2.65359 15.2479 2.75402C15.3483 2.85445 15.295 3.04213 15.1884 3.41748Z" stroke="#fff" />
								</svg>
							</label>
							<input type="text" name="event" id="event" placeholder="Tell us about your party" >
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
							<input type="number" name="num_people" id="num_people" required/>
						</div>

						<div class="fields-container fields-container-hire">
							<label for="time">
								<span>Time</span>
								<svg width="25px" height="25px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M12 7V12L14.5 13.5M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
								</svg>
							</label>
							<select required name="time" required>
								<option default disabled>Choose Time</option>
								<?php 
										
								foreach ($this->times as $time) {
									?> <option value="<?= $time ?>"><?= $time ?></option> <?php
								}
	
								?>
								<!-- <option value="3">10:00</option> -->
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

				// Construct the URL with the parameters
				$this->url = "https://bookings.designmynight.com/book?venue_id=" . urlencode($venue_id) 
				. "&source=patner&date=" . urlencode($date) 
				. "&type=" . urlencode($type) 
				. "&num_people=" . urlencode($num_people) 
				. "&time=" . urlencode($time);
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