<?php

return [
    'modules' => [
        'front_office' => [
            'label' => 'Front Office',
            'description' => 'Patient registration, appointments, check-in, clinic queue, visits, and front-desk billing.',
        ],
        'clinic' => [
            'label' => 'Clinic',
            'description' => 'Consultations, patient charts, medical records, HIV records, lab results, and prescriptions.',
        ],
        'nursing' => [
            'label' => 'Nursing',
            'description' => 'Triage, nursing patient lists, medical records, prescriptions, and nursing workflows.',
        ],
        'inpatient_ward' => [
            'label' => 'Inpatient Ward',
            'description' => 'Admissions, ward bed board, vitals, inpatient notes, transfers, and discharge work.',
        ],
        'laboratory' => [
            'label' => 'Laboratory',
            'description' => 'Lab requests, results entry, and lab catalogue management.',
        ],
        'pharmacy' => [
            'label' => 'Pharmacy',
            'description' => 'Prescriptions, dispensing, pharmacy records, stock, inventory, and purchase orders.',
        ],
        'finance' => [
            'label' => 'Finance',
            'description' => 'Billing, payments, income, expenditure, expenses, financial reports, and payroll review.',
        ],
        'human_resources' => [
            'label' => 'Human Resources',
            'description' => 'Employees, departments, contracts, attendance, leave, appraisals, payroll, and HR records.',
        ],
        'programs' => [
            'label' => 'Programs',
            'description' => 'Reports, partners, documents, emergency records, and program-level coordination.',
        ],
        'administration' => [
            'label' => 'Administration',
            'description' => 'Branches, users, roles, audit logs, service catalogue, and system configuration.',
        ],
    ],

    'default_access' => [
        'super_admin' => ['front_office', 'clinic', 'nursing', 'inpatient_ward', 'laboratory', 'pharmacy', 'finance', 'human_resources', 'programs', 'administration'],
        'branch_admin' => ['front_office', 'clinic', 'nursing', 'inpatient_ward', 'laboratory', 'pharmacy', 'finance', 'human_resources', 'programs', 'administration'],
        'doctor' => ['clinic', 'inpatient_ward', 'programs'],
        'nurse' => ['nursing', 'inpatient_ward', 'programs'],
        'receptionist' => ['front_office', 'inpatient_ward', 'programs'],
        'labtech' => ['laboratory'],
        'pharmacist' => ['pharmacy'],
        'finance_officer' => ['finance'],
        'hr_manager' => ['human_resources'],
        'line_supervisor' => ['human_resources'],
        'counselor' => ['clinic'],
        'patient' => [],
    ],
];
