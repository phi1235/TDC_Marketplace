<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Quản lý Tin tức</h1>
        <button @click="openForm()" class="px-4 py-2 rounded bg-blue-600 text-white mb-4">+ Tạo bài viết</button>
        <table class="w-full border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 text-left">Tiêu đề</th>
                    <th class="p-2 text-left">Nội dung</th>
                    <th class="p-2">Thumbnail</th>
                    <th class="p-2">Ngày tạo</th>
                    <th class="p-2 text-right">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in news" :key="item.id" class="border-t">
                    <td class="p-2 border">{{ item.title }}</td>
                    <td class="p-2 border">{{ shortText(item.content, 80) }}
                        <router-link :to="`/notifications/detail`" class="text-blue-600 hover:underline ml-1">...Xem thêm</router-link>
                    </td>
                    <td class="p-2 border"><img :src="item.thumbnail" class="w-16 h-10 object-cover" /></td>
                    <td class="p-2 text-sm border">{{ item.created_at }}</td>
                    <td class="p-2 text-right space-x-2">
                        <button @click="openForm(item)" class="px-3 py-1 bg-yellow-500 text-white rounded">Sửa</button>
                        <button @click="deleteNews(item.id)"
                            class="px-3 py-1 bg-red-600 text-white rounded">Xóa</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <!-- Modal form -->
        <div v-if="showForm" class="fixed inset-0 bg-black/50 flex items-center justify-center">
            <div class="bg-white p-6 rounded w-full max-w-lg">
                <h2 class="text-xl font-semibold mb-4">{{ editing ? 'Chỉnh sửa' : 'Tạo mới' }} bài viết</h2>
                <input v-model="form.title" placeholder="Tiêu đề" class="w-full border p-2 mb-3" />
                <input v-model="form.thumbnail" placeholder="Link ảnh thumbnail" class="w-full border p-2 mb-3" />
                <textarea v-model="form.content" rows="6" placeholder="Nội dung"
                    class="w-full border p-2 mb-4"></textarea>
                <div class="flex justify-end space-x-2">
                    <button @click="showForm = false" class="px-4 py-2 bg-gray-300 rounded">Hủy</button>
                    <button @click="saveNews()" class="px-4 py-2 bg-blue-600 text-white rounded">Lưu</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
const news = ref([
    { id: 1, title: 'Thông báo bảo trì hệ thống', thumbnail: 'https://via.placeholder.com/150', content: 'Hệ thống sẽ tiến hành bảo trì định kỳ vào lúc 23:00 ngày 30/10/2025 để nâng cấp máy chủ và tối ưu hóa hiệu năng. Trong thời gian này, toàn bộ dịch vụ sẽ tạm ngừng hoạt động trong khoảng 30 phút. Quý khách vui lòng sắp xếp công việc phù hợp để tránh ảnh hưởng đến quá trình sử dụng. Xin cảm ơn sự thông cảm và hợp tác của quý khách.', created_at: '2025-10-28' },
    { id: 2, title: 'Ra mắt tính năng mới', thumbnail: 'https://via.placeholder.com/150', content: 'Chúng tôi vừa cập nhật tính năng đặt lịch xem nhà trực tiếp.', created_at: '2025-10-27' },
    { id: 3, title: 'Chương trình khuyến mãi tháng 11', thumbnail: 'https://via.placeholder.com/150', content: 'Chúng tôi xin thông báo đến quý người dùng rằng chính sách bảo mật và điều khoản sử dụng mới sẽ được áp dụng từ ngày 01/11/2025. Bản cập nhật lần này chú trọng vào việc tăng cường bảo vệ dữ liệu cá nhân, mật khẩu và lịch sử thao tác trên hệ thống. Vui lòng đọc kỹ nội dung trước khi tiếp tục sử dụng dịch vụ để tránh phát sinh tranh chấp ngoài ý muốn.', created_at: '2025-10-25' },
    { id: 4, title: 'Lưu ý bảo mật tài khoản', thumbnail: 'https://via.placeholder.com/150', content: 'Vui lòng bật xác thực 2 lớp để đảm bảo an toàn tài khoản.', created_at: '2025-10-23' }
])
const showForm = ref(false)
const editing = ref(false)
const form = ref({ id: null, title: '', thumbnail: '', content: '' })


function openForm(item = null) {
    if (item) { editing.value = true; form.value = { ...item } }
    else { editing.value = false; form.value = { id: null, title: '', thumbnail: '', content: '' } }
    showForm.value = true
}
function saveNews() {
    if (editing.value) {
        const index = news.value.findIndex(n => n.id === form.value.id)
        if (index !== -1) news.value[index] = { ...form.value }
    } else {
        news.value.push({ ...form.value, id: Date.now(), created_at: new Date().toISOString().slice(0, 10) })
    }
    showForm.value = false
}
function deleteNews(id) {
    news.value = news.value.filter(n => n.id !== id)
}

function shortText(text, limit = 80) {
    return text.length > limit ? text.slice(0, limit) : text;
}

onMounted(() => {
    // nếu sau này dùng API thì viết ở đây, còn giờ để trống
})
</script>