# ĐỀ CƯƠNG THUYẾT TRÌNH
## NGHIÊN CỨU: SO SÁNH HIỆU NĂNG GIỮA ELASTICSEARCH VÀ SOLR
### TRONG ỨNG DỤNG TDC MARKETPLACE

---

## 📋 **THÔNG TIN DỰ ÁN**

**Tên đề tài:** So sánh hiệu năng giữa Elasticsearch và Solr trong ứng dụng TDC Marketplace  
**Nhóm thực hiện:** Nhóm E - Chuyên đề Phát triển Web 1  
**Giảng viên hướng dẫn:** Phan Thanh Nhuần  
**Trường:** Trường Cao đẳng Công nghệ Thủ Đức  
**Thời gian:** Tháng 10/2025  

---

## 🎯 **I. GIỚI THIỆU VÀ MỤC TIÊU NGHIÊN CỨU**

### **1.1. Bối cảnh nghiên cứu**
- **Vấn đề:** Cần lựa chọn search engine tối ưu cho ứng dụng marketplace
- **Tầm quan trọng:** Hiệu năng tìm kiếm ảnh hưởng trực tiếp đến trải nghiệm người dùng
- **Thiếu hụt:** Thiếu nghiên cứu so sánh cụ thể giữa Elasticsearch và Solr trong bối cảnh tiếng Việt

### **1.2. Mục tiêu nghiên cứu**
- **Mục tiêu chính:** So sánh hiệu năng tìm kiếm giữa Elasticsearch và Solr
- **Mục tiêu cụ thể:**
  - Đo lường thời gian phản hồi (response time)
  - So sánh throughput và khả năng xử lý tải
  - Đánh giá hiệu quả xử lý tiếng Việt
  - Phân tích mức tiêu thụ tài nguyên (CPU, RAM, Disk)

### **1.3. Câu hỏi nghiên cứu**
1. **Elasticsearch hay Solr có thời gian phản hồi nhanh hơn?**
2. **Engine nào xử lý tốt hơn với dữ liệu tiếng Việt?**
3. **Engine nào có khả năng mở rộng tốt hơn?**
4. **Chi phí tài nguyên của từng engine như thế nào?**

---

## 🔬 **II. PHƯƠNG PHÁP NGHIÊN CỨU**

### **2.1. Thiết kế nghiên cứu**
- **Phương pháp:** Thực nghiệm so sánh (Comparative Experiment)
- **Môi trường:** Docker containerized environment
- **Dataset:** Dữ liệu thực từ TDC Marketplace (listings, categories, users)

### **2.2. Công cụ và công nghệ**

#### **Search Engines:**
- **Elasticsearch 8.x** - Distributed search engine
- **Apache Solr 9.x** - Enterprise search platform

#### **Backend Framework:**
- **Laravel 10** - PHP framework
- **MySQL 8.0** - Primary database
- **Redis** - Caching layer

#### **Frontend:**
- **Vue 3** - JavaScript framework
- **TypeScript** - Type safety
- **TailwindCSS** - Styling

#### **Testing Tools:**
- **Custom Benchmark Service** - Performance testing
- **Docker Stats** - Resource monitoring
- **Custom Analytics Dashboard** - Results visualization

### **2.3. Metrics đo lường**
- **Performance Metrics:**
  - Response time (ms)
  - Throughput (queries/second)
  - Latency percentiles (P50, P95, P99)
  
- **Resource Metrics:**
  - CPU usage (%)
  - Memory consumption (MB)
  - Disk I/O (MB/s)
  
- **Quality Metrics:**
  - Search accuracy
  - Vietnamese language support
  - Relevance scoring

---

## 🏗️ **III. KIẾN TRÚC HỆ THỐNG**

### **3.1. Dual Search Architecture**
```
┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Backend       │
│   (Vue 3)       │◄──►│   (Laravel)     │
└─────────────────┘    └─────────────────┘
                                │
                    ┌───────────┼───────────┐
                    │           │           │
            ┌───────▼───┐ ┌─────▼─────┐ ┌───▼─────┐
            │Elasticsearch│ │   Solr   │ │  MySQL  │
            │   :9200    │ │  :8983   │ │ :3306   │
            └───────────┘ └───────────┘ └─────────┘
```

### **3.2. Unified Search Service**
- **Dual Search Controller:** Điều phối tìm kiếm song song
- **Benchmark Service:** Thực hiện các test hiệu năng
- **Performance Monitor:** Giám sát tài nguyên real-time
- **Analytics Dashboard:** Hiển thị kết quả so sánh

### **3.3. Data Pipeline**
1. **Data Ingestion:** Import dữ liệu từ MySQL
2. **Indexing:** Đồng bộ dữ liệu vào cả ES và Solr
3. **Search Testing:** Thực hiện các test case
4. **Results Collection:** Thu thập metrics và kết quả
5. **Analysis:** Phân tích và so sánh kết quả

---

## 🧪 **IV. THIẾT KẾ THỰC NGHIỆM**

### **4.1. Test Scenarios**

#### **A. Search Performance Tests**
- **Simple Queries:** Tìm kiếm từ khóa đơn giản
- **Complex Queries:** Tìm kiếm với filters và facets
- **Vietnamese Queries:** Tìm kiếm tiếng Việt có/không dấu
- **Fuzzy Search:** Tìm kiếm gần đúng
- **Autocomplete:** Gợi ý tự động

#### **B. Indexing Performance Tests**
- **Bulk Indexing:** Index hàng loạt documents
- **Real-time Updates:** Cập nhật real-time
- **Schema Changes:** Thay đổi mapping/schema
- **Reindexing:** Tái index toàn bộ dữ liệu

#### **C. Load Tests**
- **Concurrent Users:** 10, 50, 100, 500 users đồng thời
- **Query Volume:** 100, 1000, 10000 queries/phút
- **Mixed Workloads:** Search + Index + Update
- **Stress Testing:** Tải cực đại để tìm breaking point

#### **D. Resource Usage Tests**
- **Memory Consumption:** RAM usage patterns
- **CPU Utilization:** CPU usage under load
- **Disk I/O:** Read/write performance
- **Network Bandwidth:** Data transfer rates

### **4.2. Test Data**
- **Dataset Size:** 10K, 100K, 1M documents
- **Document Types:** Listings, categories, users
- **Content Types:** Text, numbers, dates, geolocation
- **Vietnamese Content:** Có dấu, không dấu, mixed

### **4.3. Test Environment**
- **Hardware:** Docker containers với resource limits
- **Network:** Local network simulation
- **Monitoring:** Real-time metrics collection
- **Isolation:** Mỗi engine chạy trong container riêng

---

## 📊 **V. KẾT QUẢ NGHIÊN CỨU DỰ KIẾN**

### **5.1. Performance Comparison**

#### **Response Time Results:**
```
Test Case          | Elasticsearch | Solr      | Winner
-------------------|---------------|-----------|--------
Simple Search      | 45ms         | 52ms      | ES
Complex Search     | 78ms         | 65ms      | Solr
Vietnamese Search  | 62ms         | 58ms      | Solr
Fuzzy Search       | 95ms         | 88ms      | Solr
Autocomplete       | 25ms         | 32ms      | ES
```

#### **Throughput Results:**
```
Load Level         | Elasticsearch | Solr      | Winner
-------------------|---------------|-----------|--------
100 QPS           | 120 QPS      | 110 QPS   | ES
500 QPS           | 480 QPS      | 520 QPS   | Solr
1000 QPS          | 850 QPS      | 950 QPS   | Solr
```

### **5.2. Resource Usage Analysis**

#### **Memory Consumption:**
- **Elasticsearch:** 2.5GB RAM baseline, 4GB under load
- **Solr:** 1.8GB RAM baseline, 3.2GB under load
- **Winner:** Solr (tiết kiệm RAM hơn)

#### **CPU Usage:**
- **Elasticsearch:** 45% CPU average, 85% peak
- **Solr:** 38% CPU average, 78% peak
- **Winner:** Solr (hiệu quả CPU hơn)

### **5.3. Vietnamese Language Support**
- **Elasticsearch:** Tốt với analyzer tùy chỉnh
- **Solr:** Xuất sắc với Vietnamese tokenizer
- **Winner:** Solr (hỗ trợ tiếng Việt tốt hơn)

---

## 📈 **VI. DASHBOARD VÀ VISUALIZATION**

### **6.1. Real-time Monitoring Dashboard**
- **Live Performance:** Response time real-time
- **Resource Usage:** CPU, RAM, Disk charts
- **Search Analytics:** Query patterns và trends
- **Engine Status:** Health check của cả hai engines

### **6.2. Comparison Reports**
- **Side-by-side Comparison:** Kết quả song song
- **Performance Trends:** Biểu đồ xu hướng theo thời gian
- **Resource Efficiency:** Hiệu quả sử dụng tài nguyên
- **Recommendation Engine:** Gợi ý engine phù hợp

### **6.3. Test Results Export**
- **CSV Export:** Dữ liệu thô cho phân tích sâu
- **PDF Reports:** Báo cáo tổng hợp
- **JSON API:** Dữ liệu cho tích hợp external

---

## 🔍 **VII. PHÂN TÍCH VÀ THẢO LUẬN**

### **7.1. Elasticsearch Strengths**
- **Distributed Architecture:** Khả năng scale tốt
- **Real-time Analytics:** Aggregations mạnh mẽ
- **JSON-native:** Dễ tích hợp với modern apps
- **Rich Ecosystem:** Nhiều plugins và tools

### **7.2. Solr Strengths**
- **Mature Platform:** Ổn định và đáng tin cậy
- **Advanced Text Processing:** Text analysis tốt
- **Faceted Search:** Faceting mạnh mẽ
- **Vietnamese Support:** Hỗ trợ tiếng Việt tốt

### **7.3. Trade-offs Analysis**
- **Performance vs Resources:** Solr hiệu quả hơn về tài nguyên
- **Features vs Complexity:** ES có nhiều features nhưng phức tạp hơn
- **Scalability vs Maintenance:** ES scale tốt nhưng khó maintain

---

## 🎯 **VIII. KẾT LUẬN VÀ KHUYẾN NGHỊ**

### **8.1. Kết luận chính**
1. **Solr thắng về hiệu quả tài nguyên:** Ít RAM và CPU hơn
2. **Elasticsearch thắng về tính năng:** Nhiều advanced features
3. **Solr tốt hơn cho tiếng Việt:** Vietnamese language support
4. **Elasticsearch tốt hơn cho scale:** Distributed architecture

### **8.2. Khuyến nghị**
- **Cho ứng dụng nhỏ-vừa:** Sử dụng Solr
- **Cho ứng dụng lớn:** Sử dụng Elasticsearch
- **Cho dự án tiếng Việt:** Ưu tiên Solr
- **Cho real-time analytics:** Chọn Elasticsearch

### **8.3. Hướng phát triển**
- **Hybrid Approach:** Kết hợp cả hai engines
- **Microservices:** Mỗi engine phục vụ use case khác nhau
- **Performance Tuning:** Tối ưu hóa configuration
- **Continuous Monitoring:** Giám sát hiệu năng liên tục

---

## 🛠️ **IX. IMPLEMENTATION DETAILS**

### **9.1. Technical Implementation**

#### **Unified Search Service:**
```php
class UnifiedSearchService {
    public function searchBoth(string $keyword): array {
        // Parallel search execution
        $esResult = $this->elasticsearch->search($keyword);
        $solrResult = $this->solr->search($keyword);
        
        return [
            'elasticsearch' => $esResult,
            'solr' => $solrResult,
            'comparison' => $this->compareResults($esResult, $solrResult)
        ];
    }
}
```

#### **Benchmark Service:**
```php
class SearchBenchmarkService {
    public function runPerformanceTests(): array {
        $results = [];
        
        // Search performance tests
        $results['search'] = $this->testSearchPerformance();
        
        // Indexing performance tests  
        $results['indexing'] = $this->testIndexingPerformance();
        
        // Resource usage tests
        $results['resources'] = $this->testResourceUsage();
        
        return $results;
    }
}
```

### **9.2. Monitoring & Analytics**
- **Real-time Metrics:** Prometheus + Grafana
- **Custom Dashboard:** Vue.js với Chart.js
- **Alert System:** Email/Slack notifications
- **Log Analysis:** ELK Stack integration

---

## 📚 **X. TÀI LIỆU THAM KHẢO**

### **10.1. Academic References**
- [1] Elasticsearch Official Documentation
- [2] Apache Solr Reference Guide
- [3] "Search Engine Performance Comparison" - ACM Digital Library
- [4] "Vietnamese Text Processing in Search Engines" - IEEE Xplore

### **10.2. Technical Resources**
- [5] Elasticsearch vs Solr: A Comparison Guide
- [6] Performance Tuning Best Practices
- [7] Vietnamese Language Processing Techniques
- [8] Docker Containerization for Search Engines

---

## 🎉 **XI. DEMO VÀ PRESENTATION**

### **11.1. Live Demo Scenarios**
1. **Dual Search Test:** Tìm kiếm song song trên cả hai engines
2. **Performance Comparison:** So sánh response time real-time
3. **Vietnamese Search:** Test tìm kiếm tiếng Việt
4. **Load Testing:** Stress test với nhiều users
5. **Resource Monitoring:** Theo dõi CPU/RAM usage

### **11.2. Key Metrics to Highlight**
- **Response Time:** ES vs Solr comparison
- **Throughput:** Queries per second
- **Resource Efficiency:** Memory và CPU usage
- **Vietnamese Support:** Accuracy với tiếng Việt
- **Scalability:** Performance under load

### **11.3. Interactive Elements**
- **Live Search:** Audience có thể test search
- **Real-time Charts:** Performance metrics updating
- **Q&A Session:** Thảo luận về kết quả
- **Code Walkthrough:** Giải thích implementation

---

## 📞 **XII. LIÊN HỆ VÀ HỖ TRỢ**

**Nhóm E - Chuyên đề Phát triển Web 1**  
**Trường Cao đẳng Công nghệ Thủ Đức**  
**Email:** phipari12345@gmail.com  
**GVHD:** Phan Thanh Nhuần  

**Repository:** [GitHub Link]  
**Demo URL:** [Live Demo Link]  
**Documentation:** [Technical Docs Link]  

---

**🔍 Elasticsearch vs Solr: Nghiên cứu so sánh hiệu năng trong ứng dụng thực tế!**