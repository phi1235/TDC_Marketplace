# Hướng dẫn Setup OpenRouter AI Chat Bot

## 1. Thêm API Key vào .env

Thêm dòng sau vào file `backend/.env` (sử dụng OpenRouter API key):

```env
OPENAI_API_KEY=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
```

**Lưu ý:** Biến `OPENAI_API_KEY` được dùng để lưu OpenRouter API key (tương thích với OpenAI format).

## 2. Chạy Migration

```bash
docker compose exec laravel php artisan migrate
```

Migration này sẽ:
- Thêm cột `is_ai` vào bảng `messages`
- Cho phép `sender_id` nullable (để AI messages có thể có sender_id = null)

## 3. Restart Laravel Container

```bash
docker compose restart laravel
```

## 4. Test AI Chat Bot

1. Đăng nhập vào hệ thống
2. Vào trang chat support: `http://localhost:5174/chat?support=1`
3. Gửi một message bất kỳ
4. AI bot sẽ tự động trả lời trong vài giây

## 5. Cách hoạt động

- AI bot chỉ hoạt động trong **support conversations** (`is_support = true`)
- Khi user gửi text message trong support chat, hệ thống sẽ:
  1. Lưu user message
  2. Gọi OpenAI API với conversation history
  3. Tạo AI response message với `is_ai = true`
  4. Broadcast cả 2 messages qua WebSocket

## 6. Troubleshooting

### AI không trả lời

1. Kiểm tra API key trong `.env` đã đúng chưa
2. Kiểm tra logs: `docker compose logs laravel | grep OpenAI`
3. Kiểm tra conversation có `is_support = true` không

### Lỗi "OpenAI API key is not configured"

- Đảm bảo đã thêm `OPENAI_API_KEY` vào `.env`
- Restart Laravel container sau khi thêm

### Lỗi timeout

- OpenAI API có thể mất 5-30 giây để trả lời
- Nếu timeout, user sẽ thấy message lỗi

## 7. Model sử dụng

- **Provider:** OpenRouter (https://openrouter.ai)
- **Model:** `openrouter/polaris-alpha` (free tier, 256k context)
- **Max tokens:** 500
- **Temperature:** 0.7
- **API Endpoint:** `https://openrouter.ai/api/v1/chat/completions`

**Thông tin model:** Polaris Alpha là một model mạnh mẽ, miễn phí, phù hợp cho các tác vụ thực tế, đặc biệt xuất sắc trong coding, tool calling và instruction following.

## 8. System Prompt

AI bot được cấu hình với system prompt về TDC Marketplace:
- Hỗ trợ về cách sử dụng website
- Hướng dẫn đăng tin, tìm kiếm
- Giải đáp về quy trình giao dịch
- Trả lời bằng tiếng Việt

## 9. Test nhanh OpenRouter API

Có thể kiểm tra key bằng lệnh cURL sau:

```bash
curl https://openrouter.ai/api/v1/chat/completions \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $OPENAI_API_KEY" \
  -d '{
  "model": "openrouter/polaris-alpha",
  "messages": [
    {
      "role": "user",
      "content": "How many r`s are in the word `strawberry?`"
    }
  ],
  "extra_body": {
    "reasoning": {
      "enabled": true
    }
  }
}'
```

Đừng quên export `OPENAI_API_KEY` trước khi chạy lệnh trên.
