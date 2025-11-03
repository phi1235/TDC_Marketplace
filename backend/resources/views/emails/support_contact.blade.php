<!DOCTYPE html>
<html>
<body>
  <h2>Yêu cầu hỗ trợ mới</h2>
  <p><strong>Họ tên:</strong> {{ $payload['name'] }}</p>
  <p><strong>Email:</strong> {{ $payload['email'] }}</p>
  <p><strong>Chủ đề:</strong> {{ $payload['topic'] ?? '(Không chọn)' }}</p>
  <p><strong>Nội dung:</strong></p>
  <pre style="white-space: pre-wrap;">{{ $payload['message'] }}</pre>
  <hr>
  <small>Gửi từ TDC Marketplace.</small>
</body>
</html>
