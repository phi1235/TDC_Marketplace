<template>
    <div class="p-6 max-w-3xl mx-auto">
        <router-link to="/notifications" class="text-blue-600 hover:underline mb-4 inline-block">← Quay lại danh
            sách</router-link>
        <h1 class="text-2xl font-bold mb-2">{{ news.title }}</h1>
        <p class="text-gray-500 text-sm mb-4">Đăng ngày: {{ news.created_at }}</p>
        <img v-if="news.thumbnail" :src="news.thumbnail" class="w-full h-64 object-cover rounded mb-4" />
        <div class="prose max-w-none" v-html="news.content"></div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const news = ref({ title: '', content: '', thumbnail: '', created_at: '' })

onMounted(() => {
    const id = route.params.id
    // Demo data tạm thời, sau này thay bằng API
    const dummy = [
        { id: 1, title: 'Thông báo bảo trì hệ thống', thumbnail: 'https://via.placeholder.com/150', content: 'Hệ thống sẽ tiến hành bảo trì định kỳ vào lúc 23:00 ngày 30/10/2025 để nâng cấp máy chủ và tối ưu hóa hiệu năng. Trong thời gian này, toàn bộ dịch vụ sẽ tạm ngừng hoạt động trong khoảng 30 phút. Quý khách vui lòng sắp xếp công việc phù hợp để tránh ảnh hưởng đến quá trình sử dụng. Xin cảm ơn sự thông cảm và hợp tác của quý khách.', created_at: '2025-10-28' },
        { id: 2, title: 'Ra mắt tính năng mới', thumbnail: 'https://via.placeholder.com/150', content: 'Chúng tôi vừa cập nhật tính năng đặt lịch xem nhà trực tiếp.', created_at: '2025-10-27' },
        { id: 3, title: 'Chương trình khuyến mãi tháng 11', thumbnail: 'https://via.placeholder.com/150', content: 'Chúng tôi xin thông báo đến quý người dùng rằng chính sách bảo mật và điều khoản sử dụng mới sẽ được áp dụng từ ngày 01/11/2025. Bản cập nhật lần này chú trọng vào việc tăng cường bảo vệ dữ liệu cá nhân, mật khẩu và lịch sử thao tác trên hệ thống. Vui lòng đọc kỹ nội dung trước khi tiếp tục sử dụng dịch vụ để tránh phát sinh tranh chấp ngoài ý muốn.', created_at: '2025-10-25' },
        { id: 4, title: 'Lưu ý bảo mật tài khoản', thumbnail: 'https://via.placeholder.com/150', content: 'Vui lòng bật xác thực 2 lớp để đảm bảo an toàn tài khoản.', created_at: '2025-10-23' }
    ]
    news.value = dummy.find(n => n.id == id) || { title: 'Không tìm thấy bài viết' }
})
</script>

<style scoped>
.prose p {
    margin-bottom: 1rem;
}
</style>