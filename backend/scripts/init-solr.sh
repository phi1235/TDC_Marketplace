#!/bin/bash

# Initialize Solr core for listings
echo "Initializing Solr core for listings..."

# Wait for Solr to be ready
echo "Waiting for Solr to be ready..."
until curl -f http://solr:8983/solr/admin/ping; do
  echo "Solr is not ready yet, waiting..."
  sleep 5
done

echo "Solr is ready!"

# Create the listings core
echo "Creating listings core..."
curl -X POST "http://solr:8983/solr/admin/cores?action=CREATE&name=listings&configSet=listings"

# Check if core was created successfully
if curl -f http://solr:8983/solr/listings/admin/ping; then
  echo "✅ Solr core 'listings' created successfully!"
else
  echo "❌ Failed to create Solr core"
  exit 1
fi

# Add some sample data for testing
echo "Adding sample data..."
curl -X POST "http://solr:8983/solr/listings/update/json/docs?commit=true" \
  -H "Content-Type: application/json" \
  -d '[
    {
      "id": "1",
      "title": "Sách giáo khoa Toán lớp 10",
      "description": "Sách giáo khoa Toán lớp 10, tình trạng tốt, ít sử dụng",
      "price": 50000,
      "original_price": 80000,
      "category_id": 1,
      "seller_id": 1,
      "condition_grade": "A",
      "status": "active",
      "created_at": "2024-01-01T00:00:00Z"
    },
    {
      "id": "2", 
      "title": "Điện thoại iPhone 12",
      "description": "Điện thoại iPhone 12, màu xanh, tình trạng tốt",
      "price": 15000000,
      "original_price": 20000000,
      "category_id": 2,
      "seller_id": 2,
      "condition_grade": "B",
      "status": "active",
      "created_at": "2024-01-02T00:00:00Z"
    }
  ]'

echo "✅ Sample data added successfully!"

echo "🎉 Solr initialization completed!"
