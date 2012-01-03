<?php

new Postype($postype = array(
	'slug' => 'employee',
	'archive' => 'employees',
	'singular' => "Employee",
	'plural' => "Employees",
	'meta' => array(
		array(
			'name' => 'job_title',
			'type' => 'text',
			'label' => "Job Title",
			'description' => "This job title for this employee",
		),
		array(
			'name' => 'computer_num',
			'type' => 'int',
			'label' => "Computer Number",
			'description' => "The employee's computer's number.",
		),
		array(
			'name' => 'hire_date',
			'type' => 'date',
			'label' => "Hire date",
			'description' => "The date the employee was hired.",
		),
		array(
			'name' => 'lunch_time',
			'type' => 'time',
			'label' => "Lunch Time",
			'description' => "Time the employee starts their lunch break.",
		),
		array(
			'name' => 'last_review',
			'type' => 'date-time',
			'label' => "Last Review",
			'description' => "The date and time of the employee's last review.",
		),
		array(
			'name' => 'shift',
			'type' => 'time-range',
			'label' => "Shift",
			'description' => "The start and end time for the employee's shift."
		),
		array(
			'name' => 'wage',
			'type' => 'money',
			'label' => "Wage",
			'description' => "The employee's hourly wage.",
		),
		array(
			'name' => 'department',
			'type' => 'radio',
			'label' => "Department",
			'description' => "The department the employee works in.",
			'options' => array(
				"Engineering",
				"Design",
				"Human Resources",
			),
		),
		array(
			'name' => 'location',
			'type' => 'select',
			'label' => "Location",
			'description' => "The location the employee is working.",
			'options' => array(
				"California",
				"Hawaii",
				"New York",
				"London",
				"Hong Kong",
				"Brisbane",
			),
		),
		array(
			'name' => 'retired',
			'type' => 'checkbox',
			'label' => "Retired",
			'description' => "This employee has retired.",
		),
		array(
			'name' => 'bio',
			'type' => 'textarea',
			'label' => "Bio",
			'description' => "Provide a short bio for this employee.",
		),
		array(
			'name' => 'clearance',
			'type' => 'slider',
			'label' => "Security Clearance",
			'description' => "The security clearance for the employee.",
			'options' => array(
				'min' => 1,
				'max' => 10,
			),
		),
		array(
			'name' => 'client_load',
			'type' => 'range',
			'label' => "Clients",
			'description' => "Number of clients the employee can handle concurrently.",
			'options' => array(
				'min' => 1,
				'max' => 100,
			),
		),
	),
));