<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif;">
  <h2>New Service Enquiry</h2>
  <p>You have a new enquiry from {{ $enquirer['name'] ?? 'Client' }}.</p>
  <ul>
    <li><strong>Name:</strong> {{ $enquirer['name'] ?? '-' }}</li>
    <li><strong>Email:</strong> {{ $enquirer['email'] ?? '-' }}</li>
    <li><strong>Phone:</strong> {{ $enquirer['phone'] ?? '-' }}</li>
  </ul>

  <h3>Details</h3>
  <p><strong>Service Type:</strong> {{ ucfirst($details['service_type'] ?? '-') }}</p>
  @if(($details['service_type'] ?? '') === 'job')
    <ul>
      <li><strong>Job ID:</strong> {{ $details['job_id'] ?? '-' }}</li>
      <li><strong>Package ID:</strong> {{ $details['package_id'] ?? '-' }}</li>
      <li><strong>Experience:</strong> {{ $details['experience_years'] ?? '-' }} years</li>
      <li><strong>Available From:</strong> {{ $details['available_from'] ?? '-' }}</li>
      <li><strong>Has Passport:</strong> {{ !empty($details['has_passport']) ? 'Yes' : 'No' }}</li>
      <li><strong>Education:</strong> {{ $details['education'] ?? '-' }}</li>
    </ul>
  @elseif(($details['service_type'] ?? '') === 'tour')
    <ul>
      <li><strong>Destination:</strong> {{ $details['destination'] ?? '-' }}</li>
      <li><strong>Dates:</strong> {{ $details['start_date'] ?? '-' }} to {{ $details['end_date'] ?? '-' }}</li>
      <li><strong>Party:</strong> {{ $details['adults'] ?? 0 }} adults, {{ $details['children'] ?? 0 }} children</li>
      <li><strong>Budget:</strong> {{ $details['budget'] ?? '-' }}</li>
      <li><strong>Accommodation:</strong> {{ $details['accommodation'] ?? '-' }}</li>
    </ul>
  @endif

  @if(!empty($details['message'] ?? ''))
    <p><strong>Message:</strong><br/>{{ $details['message'] }}</p>
  @endif

  <p>View the lead in your dashboard to follow up.</p>
</body>
  </html>

