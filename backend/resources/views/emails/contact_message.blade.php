<!doctype html>
<html>
  <body>
    <h3>Liên hệ mới</h3>

    <p><strong>Họ tên:</strong> {{ $name }}</p>
    <p><strong>Email:</strong> {{ $email }}</p>

    @if(!empty($subject))
      <p><strong>Chủ đề:</strong> {{ $subject }}</p>
    @endif

    <hr>
    <pre style="white-space: pre-wrap">{{ $content }}</pre>
  </body>
</html>
