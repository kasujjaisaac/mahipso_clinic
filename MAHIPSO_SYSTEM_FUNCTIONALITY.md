# MAHIPSO Clinic System Functionality Document

## 1. Purpose of the System

The MAHIPSO Clinic System is a branch-aware clinic and hospital management platform designed to support the full patient journey, from registration and visit check-in through consultation, laboratory, pharmacy, billing, payments, reporting, and administration.

The system is built to help the clinic manage:

- Patient registration and medical history.
- Appointments and clinic visits.
- Triage, consultation, prescriptions, and lab orders.
- Pharmacy stock, dispensing, and product audit trails.
- Automatic billing from services, lab tests, and prescriptions.
- Payments, receipts, and bill status tracking.
- Role-specific dashboards for clinical, pharmacy, laboratory, reception, and patient users.
- Branch-based access control so records belong to the correct clinic branch.
- Administrative controls for users, roles, branches, audit logs, HR, attendance, procurement, and reporting.

The system is intended to reduce paper-based work, improve accountability, reduce missed billing, and ensure that each user only accesses functionality relevant to their role and branch.

## 2. Core System Model

The system is organized around these major records:

- **Branch**: The clinic location where activities happen.
- **User**: A staff or portal account with a role and, for non-super-admin users, a branch.
- **Patient**: The person receiving care.
- **Appointment**: A planned patient visit.
- **Visit**: The actual patient encounter at the clinic.
- **Vitals**: Triage observations recorded during a visit.
- **Medical Record**: Clinical consultation notes, diagnosis, treatment plan, and follow-up notes.
- **Lab Test**: Laboratory order and result linked to a patient or visit.
- **Prescription Order**: Medication order created by a clinician and dispensed by pharmacy.
- **Bill**: Financial charge generated for a visit or patient service.
- **Bill Item**: Individual charge line such as consultation, lab test, or medicine.
- **Payment Transaction**: Payment received against a bill.
- **Inventory/Product**: Stock records used by pharmacy and stores.
- **Audit Log**: System activity record showing who performed important actions.

Most operational records are connected to a branch so branch staff can work only with their own branch data.

## 3. User Roles and Access Control

The system uses role-based access control combined with branch-based data separation.

### 3.1 Super Admin

The super admin has the highest level of access. This user can:

- Manage branches.
- Add, edit, and manage system users.
- Assign roles to users.
- Assign users to specific branches.
- Manage roles and permissions.
- View cross-branch data where appropriate.
- View audit logs for administrative oversight.
- Access system-wide reporting and configuration areas.

Important rule:

- When the super admin adds a non-super-admin user, the user must be attached to a specific branch. This ensures all activities created by that user can be traced back to a branch.

### 3.2 Branch Admin

The branch admin manages activities within their own branch. This user can:

- View branch-level reports.
- Monitor staff and operational records for their branch.
- Access audit logs related to their branch.
- Oversee patients, visits, billing, pharmacy, and lab activity for their branch according to assigned permissions.

Branch admins should not manage unrelated branches.

### 3.3 Doctor

Doctors focus on clinical care. They can:

- View assigned patients and visits.
- Access the clinic queue.
- Review vitals.
- Create and update medical records.
- Add diagnoses, clinical notes, treatment plans, and follow-up notes.
- Order lab tests.
- Create prescription orders.
- Review completed lab results.
- Move patients through the clinical workflow.

Doctors work within their assigned branch.

### 3.4 Nurse

Nurses support triage and clinical workflow. They can:

- View branch patients and visits.
- Record vitals.
- Assist with visit flow.
- Add clinical support notes where permitted.
- Support lab or prescription workflows depending on assigned permissions.

When vitals are recorded, the visit can move from triage toward consultation.

### 3.5 Lab Technician

Lab technicians manage laboratory workflow. They can:

- View lab orders assigned to their branch.
- Record lab results.
- Mark tests as completed.
- Add result flags and findings.
- Return completed lab work to the consultation flow.

Lab technicians do not manage unrelated billing, pharmacy, or user administration functions.

### 3.6 Pharmacist

Pharmacists manage prescription dispensing and pharmacy stock. They can:

- View prescription orders.
- Dispense prescribed medicines.
- Reduce product stock when medicine is dispensed.
- Create pharmacy audit records.
- Support automatic billing for dispensed medicine.

The system checks prescriptions against recorded allergies and configured drug interactions before allowing unsafe prescriptions.

### 3.7 Receptionist

Receptionists support front-desk operations. They can:

- Register patients.
- Schedule appointments.
- Check patients into visits.
- View the clinic queue.
- Support billing handover where permitted.

Reception is central to starting the patient journey.

### 3.8 Cashier or Billing User

Billing users manage bills and payments. They can:

- View bills for their branch.
- Generate bills from visits.
- Review bill items.
- Receive payments.
- Print or view receipts.
- Track paid, partially paid, and unpaid bills.

### 3.9 Patient Portal User

Patients can access a limited portal view. The patient dashboard can show:

- Their visits.
- Their lab results.
- Their bills.
- Basic personal clinical history visible to the patient.

The current patient portal foundation links patients by email. A future improvement should link users directly to patient records using a dedicated `patient_id`.

## 4. Branch-Based Data Control

Branch control is one of the most important parts of the system.

Each staff user, except the super admin, belongs to a branch. When that user creates or manages operational data, the activity is expected to be connected to that branch. This applies to areas such as:

- Patients.
- Visits.
- Appointments.
- Bills.
- Lab tests.
- Inventory records.
- Pharmacy activity.
- Employee records.
- Audit logs.

This design supports multi-branch clinic management. It prevents one branch from accidentally seeing or changing another branch's records and makes reporting more accurate.

## 5. Patient Flow From Visit to Billing

The system supports a structured patient journey.

### Step 1: Patient Registration

The receptionist registers the patient or finds an existing patient record. The patient profile stores identifying details and clinical history references.

### Step 2: Appointment or Walk-In Check-In

The patient may arrive through an appointment or as a walk-in. A visit record is created and linked to:

- Patient.
- Branch.
- Appointment, if applicable.
- Visit stage.
- Visit status.

New visits begin in the `checked_in` stage.

### Step 3: Clinic Queue

The clinic queue shows active patients and their current stage. Staff can see which patient is waiting for:

- Triage.
- Consultation.
- Laboratory.
- Pharmacy.
- Billing.
- Completion.

This gives the clinic a real-time operational view of patient movement.

### Step 4: Triage and Vitals

Nursing staff record vitals such as blood pressure, temperature, pulse, and other observations. Once vitals are captured, the visit can move to consultation.

### Step 5: Consultation

The doctor reviews the patient, vitals, history, and symptoms. The doctor may:

- Create a medical record.
- Add diagnosis.
- Add treatment notes.
- Request laboratory tests.
- Prescribe medicine.
- Recommend follow-up.

### Step 6: Laboratory Orders and Results

If lab tests are required, the doctor creates lab orders. The lab technician processes the tests, records results, and marks them as completed.

Completed results return the patient flow back to consultation so the clinician can make decisions based on the findings.

### Step 7: Prescription Orders

If medicine is required, the doctor creates a prescription order. The system checks:

- Patient allergies.
- Configured drug interactions.

If a safety conflict is detected, the prescription is blocked until corrected.

### Step 8: Pharmacy Dispensing

The pharmacist reviews the prescription order and dispenses medicines. Dispensing can:

- Reduce stock.
- Create stock audit activity.
- Prepare prescription items for billing.
- Move the visit toward billing.

### Step 9: Automatic Billing

Billing can be generated from the visit. The system can create bill items from:

- Consultation service.
- Lab tests.
- Prescription items.
- Other service catalog charges.

This reduces missed billing and creates a clear financial record.

### Step 10: Payment and Receipt

The billing user receives payment against the bill. The system records:

- Amount paid.
- Payment method.
- Payment reference.
- Payment date.
- Receiving user.

The bill status updates automatically:

- Unpaid.
- Partially paid.
- Paid.

A receipt can be viewed or printed for the payment transaction.

### Step 11: Visit Completion

After consultation, lab, pharmacy, and billing are complete, the visit can be closed. If the visit came from an appointment, the appointment can also be marked as completed.

## 6. Main Functional Modules

### 6.1 Authentication and Security

The system includes user login and account security controls.

Security features include:

- Role-based access.
- Branch-based restrictions.
- Login failure tracking.
- Temporary account lock after repeated failed attempts.
- Password-change tracking fields.
- Active user checks.

If a user repeatedly fails to log in, the account can be locked for a short period to reduce brute-force login risk.

### 6.2 User and Role Management

Administrative users can manage system accounts and roles.

Main functions include:

- Create users.
- Assign roles.
- Assign branch.
- Activate or deactivate users.
- Manage user access.
- View audit activity.

The super admin is responsible for creating users correctly and ensuring every non-super-admin user has a branch.

### 6.3 Branch Management

Branches represent clinic locations. Branches are used for:

- Staff assignment.
- Patient and visit ownership.
- Branch-level reporting.
- Inventory separation.
- Billing and payment separation.
- Audit visibility.

Branch design makes the system suitable for clinics with more than one operating location.

### 6.4 Patient Management

The patient module manages the clinic's patient registry.

Functions include:

- Register patients.
- View patient profile.
- Update patient details.
- View patient visits.
- View allergies.
- Add or remove patient allergies.
- View related bills, lab tests, and medical records where permitted.

The patient profile acts as the central record for clinical and financial history.

### 6.5 Appointments

The appointment module helps the clinic plan patient visits.

Functions include:

- Create appointments.
- Assign appointment date and time.
- Link appointment to patient.
- Track appointment status.
- Convert appointment activity into a visit when the patient arrives.

Appointments support planned care, while visits capture actual care delivery.

### 6.6 Visits and Clinic Queue

Visits are the central workflow record for patient care.

Visit stages include:

- `checked_in`
- `triage`
- `consultation`
- `laboratory`
- `pharmacy`
- `billing`
- `completed`

The queue helps staff know where each patient is in the clinic flow.

### 6.7 Vitals and Triage

The vitals module captures basic clinical observations before consultation.

Typical data includes:

- Temperature.
- Blood pressure.
- Pulse.
- Weight.
- Respiratory rate.
- Other observations depending on the form configuration.

Vitals are linked to a visit and support better clinical decision-making.

### 6.8 Medical Records

Medical records store clinical consultation information.

They may include:

- Chief complaint.
- History.
- Examination findings.
- Diagnosis.
- Treatment plan.
- Clinical notes.
- Follow-up instructions.

Access is restricted to authorized clinical users and properly scoped branch staff.

### 6.9 HIV Records

The system includes HIV record functionality for specialized clinical tracking.

This supports clinics that manage HIV-related services and need structured patient follow-up records. Access should remain tightly controlled because HIV data is highly sensitive.

### 6.10 Laboratory

The laboratory module supports ordering and result management.

Functions include:

- Create lab orders.
- Link lab orders to visits.
- Use lab catalog items.
- Add test price.
- Mark billable tests.
- Record results.
- Add result flags.
- Mark tests completed.
- Send patient back to consultation after results.

Lab tests can contribute automatically to billing when they are billable.

### 6.11 Lab Catalogue

The lab catalogue stores standard lab tests and prices.

This helps ensure:

- Consistent lab test names.
- Standard pricing.
- Easier billing.
- Better reporting by test type.

### 6.12 Prescription Orders

The prescription module allows clinical users to create medicine orders.

Functions include:

- Create prescription order from a visit.
- Add prescription items.
- Specify medicine, dosage, frequency, duration, and quantity.
- Check allergy risks.
- Check configured drug interactions.
- Send prescription to pharmacy.

This module connects consultation directly to pharmacy and billing.

### 6.13 Pharmacy

The pharmacy module manages medicine dispensing and stock movement.

Functions include:

- View prescriptions.
- Dispense medicines.
- Reduce medicine stock.
- Track product audit logs.
- Record pharmacy sales.
- Support pharmacy reports.

Pharmacy activity is connected to patient care and billing.

### 6.14 Inventory

Inventory supports stock tracking for medicines and other clinic items.

Functions include:

- Manage stock items.
- Track quantities.
- Monitor branch inventory.
- Support stock updates.
- Connect pharmacy dispensing to stock reduction.

Inventory records are branch-aware so each branch can manage its own stock position.

### 6.15 Billing

The billing module manages patient charges.

Functions include:

- Create bills.
- Generate bills from visits.
- Add bill items.
- Include consultation fees.
- Include lab test charges.
- Include prescription charges.
- Track bill status.
- View branch bills.

Automatic billing helps the clinic reduce missed charges and makes the patient financial record clearer.

### 6.16 Service Catalogue

The service catalogue stores billable clinic services.

Examples include:

- Consultation.
- Procedure.
- Nursing service.
- Review fee.
- Other clinic service charges.

The catalogue supports standard pricing and automatic billing.

### 6.17 Payments and Receipts

The payment module records money received against bills.

Functions include:

- Receive payment.
- Record amount.
- Record payment method.
- Record reference number.
- Update bill status.
- Generate payment receipt.

The payment engine supports partial and full payments.

### 6.18 Reporting

The reporting module gives managers operational and financial visibility.

Reports can include:

- Patient counts.
- Visit counts.
- Billing totals.
- Payment totals.
- Lab activity.
- Pharmacy activity.
- Branch performance indicators.

Reports are branch-aware so branch users see their own branch performance while super admin can view wider system activity.

### 6.19 Role-Specific Dashboards

The dashboard shown to each user depends on role.

Examples:

- Doctor dashboard: consultations, queue, lab results, prescriptions.
- Nurse dashboard: triage and vitals activity.
- Lab technician dashboard: pending lab orders and completed results.
- Pharmacist dashboard: prescriptions to dispense and pharmacy tasks.
- Receptionist dashboard: appointments, check-ins, and patient flow.
- Patient dashboard: personal visits, lab results, and bills.

This prevents users from being overloaded with irrelevant modules.

### 6.20 HR and Attendance

The system includes HR-related functionality.

Functions include:

- Employee records.
- Departments.
- Contracts.
- Leave records.
- Staff attendance tracking.

Attendance helps management monitor staff presence and branch operations.

### 6.21 Procurement

The procurement foundation supports purchase order management.

Functions include:

- Supplier records.
- Purchase orders.
- Procurement status tracking.

This module can later be expanded with purchase order line items, approval workflows, goods receiving, and supplier payment tracking.

### 6.22 Expenses and Finance

The finance area supports expense and financial management.

Functions include:

- Expense recording.
- Financial summaries.
- Billing and payment visibility.
- Branch financial tracking.

This provides basic operational finance support for the clinic.

### 6.23 Notifications

Notifications support internal communication and reminders.

They can be used to alert users about relevant activities such as appointments, workflow tasks, or operational updates depending on system configuration.

### 6.24 Messaging

The messaging module allows controlled communication between users.

Messages are scoped so users can access messages where they are the sender or recipient.

### 6.25 Documents

The document module supports storage or management of clinic-related documents.

This can be used for staff documents, patient-related documents, or administrative files depending on configuration and permissions.

### 6.26 Partners and Emergencies

The system includes partner and emergency management areas.

These can support:

- External partner records.
- Emergency cases.
- Emergency response documentation.
- Referral or support coordination.

### 6.27 Audit Logs

Audit logs record important activity in the system.

Audit records can include:

- Acting user.
- Branch.
- Action performed.
- Model or record affected.
- Timestamp.

Audit logs are important for accountability, security review, and management oversight.

### 6.28 Mobile and Offline Readiness

The system includes a progressive web app foundation.

Current features include:

- Web app manifest.
- Service worker registration.
- Basic asset caching foundation.

This improves mobile readiness. Full offline transactional sync is a future enhancement.

## 7. Important Data Flow Examples

### 7.1 Consultation to Lab to Billing

1. Doctor sees patient during visit.
2. Doctor orders lab test.
3. Lab technician records result.
4. Completed result returns to consultation.
5. Billing generates bill from visit.
6. Lab charge appears as a bill item if billable.
7. Patient pays.
8. Receipt is generated.

### 7.2 Consultation to Prescription to Billing

1. Doctor creates prescription.
2. System checks allergy and interaction risks.
3. Pharmacist dispenses medicine.
4. Stock reduces.
5. Product audit is recorded.
6. Prescription charge is included in bill.
7. Patient pays at billing.

### 7.3 Super Admin User Creation

1. Super admin opens user management.
2. Super admin creates a user.
3. Super admin selects the user's role.
4. Super admin assigns the user to a branch.
5. The user logs in and only accesses allowed branch data.
6. The user's activities are traceable through branch-linked records and audit logs.

## 8. Access Control Summary

The system protects data using three main controls:

### 8.1 Role Control

The user's role determines which modules they can access.

For example:

- Doctors access clinical functions.
- Lab technicians access lab functions.
- Pharmacists access pharmacy functions.
- Billing users access bills and payments.
- Super admin accesses administrative configuration.

### 8.2 Branch Control

The user's branch determines which records they can see and manage.

For example:

- A branch pharmacist should see pharmacy work for their branch.
- A branch billing user should see bills for their branch.
- A branch admin should see branch activity, not unrelated branch data.

### 8.3 Policy and Controller Checks

Controllers and policies enforce permission checks before users can create, view, update, or complete sensitive actions.

This protects clinical, financial, pharmacy, and administrative data.

## 9. Current Strengths

The system already has strong foundations for a professional clinic system:

- End-to-end patient visit flow.
- Branch-based access model.
- Super admin and branch user separation.
- Clinical workflow stages.
- Lab orders and results.
- Prescription orders and pharmacy dispensing.
- Stock reduction on dispensing.
- Automatic billing from services.
- Payment recording and receipts.
- Role-specific dashboards.
- Audit logging.
- Patient portal foundation.
- Procurement and attendance foundations.
- PWA/mobile-readiness foundation.

## 10. Current Limitations and Recommended Next Improvements

The system has many professional clinic features, but the following areas can still be improved to reach a more advanced hospital-management level.

### 10.1 Patient Portal Linking

Current patient portal matching is based on email. A stronger model should add a direct `patient_id` to user accounts for accurate patient-user linking.

### 10.2 Advanced Billing

Future improvements should include:

- Receipt number sequences.
- Cashier shifts.
- End-of-day reconciliation.
- Refunds.
- Insurance claims.
- Discounts and approval controls.
- Credit balances and deposits.

### 10.3 Procurement

The procurement module should be expanded with:

- Purchase order line items.
- Approval workflow.
- Goods received notes.
- Supplier invoices.
- Stock receiving into inventory.

### 10.4 Pharmacy Safety

The system currently supports allergy and interaction checks using configured records. It can be improved by integrating a full drug database for:

- Drug-to-drug interactions.
- Contraindications.
- Pregnancy warnings.
- Pediatric dosage checks.
- Duplicate therapy warnings.

### 10.5 Offline Capability

The current PWA setup is a foundation. Full offline mode should support:

- Offline patient lookup.
- Offline triage capture.
- Offline visit notes.
- Background synchronization.
- Conflict resolution.

### 10.6 Clinical Coding

The system can be improved with:

- ICD-10 diagnosis coding.
- Standard procedure codes.
- Structured clinical templates.
- Referral letters.
- Discharge summaries.

### 10.7 Laboratory Quality Control

Future lab improvements should include:

- Sample collection tracking.
- Sample numbers and barcodes.
- Lab result verification.
- Critical result alerts.
- Machine integration.

### 10.8 Inventory Control

Inventory can be improved with:

- Batch numbers.
- Expiry tracking.
- Minimum stock alerts.
- Stock transfers between branches.
- Stock adjustment approvals.
- Physical count reconciliation.

### 10.9 Reporting and Analytics

Reporting can be expanded with:

- Exportable reports.
- Date filters.
- Department filters.
- Revenue by service.
- Drug consumption reports.
- Disease surveillance reports.
- Staff productivity reports.

### 10.10 Appointment and Queue Optimization

Future improvements should include:

- Appointment reminders.
- No-show tracking.
- Queue priority.
- Emergency fast-tracking.
- Estimated waiting time.

## 11. Setup and Migration Note

Because the system includes database-backed functionality, the latest migrations should be run after pulling or deploying the updated code:

```bash
php artisan migrate
```

Recommended verification after setup:

```bash
php artisan test
```

The system should also be tested manually using users from different roles and branches to confirm that each user sees only the correct modules and data.

## 12. Final Summary

The MAHIPSO Clinic System is now structured as a complete branch-aware clinic management platform. It supports the major operational areas of a professional clinic: registration, appointment handling, clinic queue, triage, consultation, lab, pharmacy, billing, payments, reporting, HR, procurement, audit control, and role-specific dashboards.

Its most important strength is that clinical and financial workflows are connected. A patient visit can move from check-in to triage, consultation, lab, pharmacy, billing, payment, and completion while keeping records linked to the patient, branch, staff user, and billable services.

With further improvements in insurance, advanced inventory, drug database integration, offline sync, and clinical coding, the system can continue moving toward a more complete hospital management platform.
