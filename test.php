<?php

new Postype(array(
	'slug' => 'employee',
	'archive' => 'employees',
	'singular' => "Employee",
	'plural' => "Employees",
	'meta' => array(
		array(
			'name' => 'job_title',
			'type' => 'text',
			'label' => "Job Title",
			'desc' => "This job title for this employee",
		),
		array(
			'name' => 'computer_num',
			'type' => 'int',
			'label' => "Computer Number",
			'desc' => "The employee's computer's number.",
		),
		array(
			'name' => 'birthday',
			'type' => 'date',
			'label' => "Birthday",
			'desc' => "The birthday of the employee.",
		),
		array(
			'name' => 'lunch_time',
			'type' => 'time',
			'label' => "Lunch Time",
			'desc' => "Time the employee starts their lunch break.",
		),
		array(
			'name' => 'last_review',
			'type' => 'date-time',
			'label' => "Last Review",
			'desc' => "The date and time of the employee's last review.",
		),
		array(
			'name' => 'shift',
			'type' => 'time-range',
			'label' => "Shift",
			'desc' => "The start and end time for the employee's shift."
		),
		array(
			'name' => 'salary',
			'type' => 'money',
			'label' => "Salary",
			'desc' => "The employee's annual salary.",
		),
		array(
			'name' => 'department',
			'type' => 'radio',
			'label' => "Department",
			'desc' => "The department the employee works in.",
			'options' => array(
				'engineering' => "Engineering",
				'design' => "Design",
				'human resources' => "Human Resources",
			),
		),
		array(
			'name' => 'favorite_color',
			'type' => 'select',
			'label' => "Favorite Color",
			'desc' => "What! Is your favorite color?",
			'options' => array(
				'red' => "Red",
				'orange' => "Orange",
				'yellow' => "Yellow",
				'green' => "Green",
				'blue' => "Blue",
				'purple' => "Purple",
				'white' => "White",
				'black' => "Black",
				'brown' => "Brown",
			),
		),
		array(
			'name' => 'retired',
			'type' => 'checkbox',
			'label' => "Retired",
			'desc' => "This employee has retired.",
		),
		array(
			'name' => 'bio',
			'type' => 'textarea',
			'label' => "Bio",
			'desc' => "Provide a short bio for this employee.",
		),
		array(
			'name' => 'rank',
			'type' => 'slider',
			'label' => "Rank",
			'desc' => "The rank of the employee (1 to 10).",
			'min' => 1,
			'max' => 10,
		),
		array(
			'name' => 'days_per_week',
			'type' => 'range',
			'label' => "Days per week",
			'desc' => "Number of days worked per week (1 to 7).",
			'min' => 1,
			'max' => 7,
		),
	),
));