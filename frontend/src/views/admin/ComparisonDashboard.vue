<template>
  <div class="comparison-dashboard">
    <!-- Header -->
    <div class="dashboard-header">
      <h1 class="text-3xl font-bold text-gray-900 mb-2">
        🔍 Search Engine Comparison Dashboard
      </h1>
      <p class="text-gray-600 mb-6">
        So sánh hiệu năng giữa Elasticsearch và Apache Solr cho hệ thống marketplace
      </p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- Elasticsearch Stats -->
      <div class="bg-white p-6 rounded-lg shadow-sm border">
        <div class="flex items-center mb-4">
          <div class="p-2 bg-blue-100 rounded-lg">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Elasticsearch</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.elasticsearch.avgResponseTime }}ms</p>
          </div>
        </div>
        <div class="space-y-1 text-xs text-gray-500">
          <div class="flex justify-between">
            <span>Min:</span>
            <span>{{ stats.elasticsearch.minResponseTime || 0 }}ms</span>
          </div>
          <div class="flex justify-between">
            <span>Max:</span>
            <span>{{ stats.elasticsearch.maxResponseTime || 0 }}ms</span>
          </div>
          <div class="flex justify-between">
            <span>Consistency:</span>
            <span>{{ stats.elasticsearch.consistency || 0 }}</span>
          </div>
        </div>
      </div>

      <!-- Solr Stats -->
      <div class="bg-white p-6 rounded-lg shadow-sm border">
        <div class="flex items-center mb-4">
          <div class="p-2 bg-orange-100 rounded-lg">
            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Apache Solr</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.solr.avgResponseTime }}ms</p>
          </div>
        </div>
        <div class="space-y-1 text-xs text-gray-500">
          <div class="flex justify-between">
            <span>Min:</span>
            <span>{{ stats.solr.minResponseTime || 0 }}ms</span>
          </div>
          <div class="flex justify-between">
            <span>Max:</span>
            <span>{{ stats.solr.maxResponseTime || 0 }}ms</span>
          </div>
          <div class="flex justify-between">
            <span>Consistency:</span>
            <span>{{ stats.solr.consistency || 0 }}</span>
          </div>
        </div>
      </div>

      <!-- Resource Usage -->
      <div class="bg-white p-6 rounded-lg shadow-sm border">
        <div class="flex items-center mb-4">
          <div class="p-2 bg-green-100 rounded-lg">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Resource Usage</p>
            <p class="text-lg font-semibold text-gray-900">{{ resourceStats.overall.memory }}MB</p>
          </div>
        </div>
        <div class="space-y-1 text-xs text-gray-500">
          <div class="flex justify-between">
            <span>CPU:</span>
            <span>{{ resourceStats.overall.cpu }}%</span>
          </div>
          <div class="flex justify-between">
            <span>Disk:</span>
            <span>{{ resourceStats.overall.disk }}MB</span>
          </div>
          <div class="flex justify-between">
            <span>Network:</span>
            <span>{{ resourceStats.overall.network }}KB/s</span>
          </div>
        </div>
      </div>

      <!-- Quality Score -->
      <div class="bg-white p-6 rounded-lg shadow-sm border">
        <div class="flex items-center mb-4">
          <div class="p-2 bg-purple-100 rounded-lg">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Quality Score</p>
            <p class="text-2xl font-semibold text-gray-900">{{ qualityMetrics.overall.f1_score }}%</p>
          </div>
        </div>
        <div class="space-y-1 text-xs text-gray-500">
          <div class="flex justify-between">
            <span>Precision:</span>
            <span>{{ qualityMetrics.overall.precision }}%</span>
          </div>
          <div class="flex justify-between">
            <span>Recall:</span>
            <span>{{ qualityMetrics.overall.recall }}%</span>
          </div>
          <div class="flex justify-between">
            <span>Vietnamese:</span>
            <span>{{ qualityMetrics.overall.vietnamese_score }}%</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Performance Trends -->
    <div class="bg-white p-6 rounded-lg shadow-sm border mb-8">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Performance Trends (Last 7 Days)</h3>
      <div class="grid grid-cols-7 gap-4">
        <div v-for="(day, index) in trendsData.labels" :key="day" class="text-center">
          <div class="text-sm font-medium text-gray-600 mb-2">{{ day }}</div>
          <div class="space-y-1">
            <div class="text-xs text-blue-600">ES: {{ trendsData.elasticsearch[index] }}ms</div>
            <div class="text-xs text-orange-600">Solr: {{ trendsData.solr[index] }}ms</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Resource Usage Trends -->
    <div class="bg-white p-6 rounded-lg shadow-sm border mb-8">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">🖥️ Resource Usage Trends</h3>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div>
          <h4 class="text-md font-medium text-gray-700 mb-2">Memory Usage</h4>
          <div class="space-y-4">
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">Elasticsearch</span>
              <div class="flex items-center space-x-2">
                <div class="w-20 bg-gray-200 rounded-full h-2">
                  <div class="bg-blue-500 h-2 rounded-full" style="width: 75%"></div>
                </div>
                <span class="text-sm font-medium text-blue-600">512.5MB</span>
              </div>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">Solr</span>
              <div class="flex items-center space-x-2">
                <div class="w-20 bg-gray-200 rounded-full h-2">
                  <div class="bg-orange-500 h-2 rounded-full" style="width: 60%"></div>
                </div>
                <span class="text-sm font-medium text-orange-600">384.2MB</span>
              </div>
            </div>
          </div>
        </div>
        <div>
          <h4 class="text-md font-medium text-gray-700 mb-2">CPU Usage</h4>
          <div class="space-y-4">
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">Elasticsearch</span>
              <div class="flex items-center space-x-2">
                <div class="w-20 bg-gray-200 rounded-full h-2">
                  <div class="bg-blue-500 h-2 rounded-full" style="width: 15%"></div>
                </div>
                <span class="text-sm font-medium text-blue-600">15.2%</span>
              </div>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">Solr</span>
              <div class="flex items-center space-x-2">
                <div class="w-20 bg-gray-200 rounded-full h-2">
                  <div class="bg-orange-500 h-2 rounded-full" style="width: 12%"></div>
                </div>
                <span class="text-sm font-medium text-orange-600">12.8%</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Search Analytics -->
    <div class="bg-white p-6 rounded-lg shadow-sm border mb-8">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">🔍 Search Analytics</h3>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div>
          <h4 class="text-md font-medium text-gray-700 mb-2">Popular Keywords</h4>
          <div class="space-y-2">
            <div v-for="keyword in popularKeywords" :key="keyword.term" class="flex justify-between items-center">
              <span class="text-sm text-gray-600">{{ keyword.term }}</span>
              <span class="text-sm font-medium text-blue-600">{{ keyword.count }}</span>
            </div>
          </div>
        </div>
        <div>
          <h4 class="text-md font-medium text-gray-700 mb-2">Search Volume (Last 24h)</h4>
          <div class="space-y-2">
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">00:00</span>
              <span class="text-sm font-medium text-green-600">12 searches</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">12:00</span>
              <span class="text-sm font-medium text-green-600">45 searches</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">20:00</span>
              <span class="text-sm font-medium text-green-600">22 searches</span>
            </div>
          </div>
        </div>
        <div>
          <h4 class="text-md font-medium text-gray-700 mb-2">Engine Preference</h4>
          <div class="space-y-2">
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">Elasticsearch</span>
              <span class="text-sm font-medium text-blue-600">45%</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">Solr</span>
              <span class="text-sm font-medium text-orange-600">35%</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">Both</span>
              <span class="text-sm font-medium text-green-600">20%</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Detailed Metrics Comparison -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <!-- Performance Details -->
      <div class="bg-white p-6 rounded-lg shadow-sm border">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">⚡ Performance Details</h3>
        <div class="space-y-4">
          <div class="flex justify-between items-center">
            <span class="text-sm font-medium text-gray-600">Response Time Range</span>
            <div class="text-right">
              <div class="text-xs text-blue-600">ES: {{ stats.elasticsearch.minResponseTime }}-{{ stats.elasticsearch.maxResponseTime }}ms</div>
              <div class="text-xs text-orange-600">Solr: {{ stats.solr.minResponseTime }}-{{ stats.solr.maxResponseTime }}ms</div>
            </div>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm font-medium text-gray-600">Consistency Score</span>
            <div class="text-right">
              <div class="text-xs text-blue-600">ES: {{ stats.elasticsearch.consistency }}</div>
              <div class="text-xs text-orange-600">Solr: {{ stats.solr.consistency }}</div>
            </div>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm font-medium text-gray-600">Avg Results per Query</span>
            <div class="text-right">
              <div class="text-xs text-blue-600">ES: {{ stats.elasticsearch.avgResults }}</div>
              <div class="text-xs text-orange-600">Solr: {{ stats.solr.avgResults }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Resource Usage Details -->
      <div class="bg-white p-6 rounded-lg shadow-sm border">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">🖥️ Resource Usage</h3>
        <div class="space-y-4">
          <div class="flex justify-between items-center">
            <span class="text-sm font-medium text-gray-600">Memory Usage</span>
            <div class="text-right">
              <div class="text-xs text-blue-600">ES: {{ resourceStats.elasticsearch.memory }}MB</div>
              <div class="text-xs text-orange-600">Solr: {{ resourceStats.solr.memory }}MB</div>
            </div>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm font-medium text-gray-600">CPU Usage</span>
            <div class="text-right">
              <div class="text-xs text-blue-600">ES: {{ resourceStats.elasticsearch.cpu }}%</div>
              <div class="text-xs text-orange-600">Solr: {{ resourceStats.solr.cpu }}%</div>
            </div>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm font-medium text-gray-600">Disk Usage</span>
            <div class="text-right">
              <div class="text-xs text-blue-600">ES: {{ resourceStats.elasticsearch.disk }}MB</div>
              <div class="text-xs text-orange-600">Solr: {{ resourceStats.solr.disk }}MB</div>
            </div>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm font-medium text-gray-600">Network I/O</span>
            <div class="text-right">
              <div class="text-xs text-blue-600">ES: {{ resourceStats.elasticsearch.network }}KB/s</div>
              <div class="text-xs text-orange-600">Solr: {{ resourceStats.solr.network }}KB/s</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Dual Search Test -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
      <DualSearchTest />
    </div>

    <!-- Load Testing -->
    <div class="bg-white p-6 rounded-lg shadow-sm border mb-8">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">🚀 Load Testing</h2>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div>
          <h3 class="text-lg font-medium text-gray-700 mb-4">Test Configuration</h3>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Concurrent Users</label>
              <input v-model="loadTestConfig.concurrentUsers" type="number" min="1" max="100" 
                     class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Requests per User</label>
              <input v-model="loadTestConfig.requestsPerUser" type="number" min="1" max="50" 
                     class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Test Duration (seconds)</label>
              <input v-model="loadTestConfig.testDuration" type="number" min="10" max="300" 
                     class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Engine</label>
              <select v-model="loadTestConfig.engine" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="both">Both Engines</option>
                <option value="elasticsearch">Elasticsearch Only</option>
                <option value="solr">Solr Only</option>
              </select>
            </div>
            <button @click="runLoadTest" :disabled="isLoadTesting" 
                    class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 disabled:opacity-50">
              {{ isLoadTesting ? 'Running Test...' : 'Start Load Test' }}
            </button>
          </div>
        </div>
        <div>
          <h3 class="text-lg font-medium text-gray-700 mb-4">Test Results</h3>
          <div v-if="loadTestResults" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div class="bg-blue-50 p-4 rounded-lg">
                <h4 class="font-medium text-blue-900">Elasticsearch</h4>
                <p class="text-sm text-blue-700">RPS: {{ loadTestResults.elasticsearch.requests_per_second }}</p>
                <p class="text-sm text-blue-700">Success: {{ loadTestResults.elasticsearch.success_rate }}%</p>
                <p class="text-sm text-blue-700">Avg: {{ loadTestResults.elasticsearch.avg_response_time }}ms</p>
              </div>
              <div class="bg-orange-50 p-4 rounded-lg">
                <h4 class="font-medium text-orange-900">Solr</h4>
                <p class="text-sm text-orange-700">RPS: {{ loadTestResults.solr.requests_per_second }}</p>
                <p class="text-sm text-orange-700">Success: {{ loadTestResults.solr.success_rate }}%</p>
                <p class="text-sm text-orange-700">Avg: {{ loadTestResults.solr.avg_response_time }}ms</p>
              </div>
            </div>
          </div>
          <div v-else class="text-gray-500 text-center py-8">
            No load test results yet
          </div>
        </div>
      </div>
    </div>

    <!-- Real-time Monitoring -->
    <div class="bg-white p-6 rounded-lg shadow-sm border mb-8">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">📊 Real-time Monitoring</h2>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-green-50 p-4 rounded-lg">
          <h3 class="font-medium text-green-900 mb-2">System Health</h3>
          <div class="space-y-1 text-sm">
            <div class="flex justify-between">
              <span>Memory:</span>
              <span>{{ systemMetrics.memory_usage.percentage }}%</span>
            </div>
            <div class="flex justify-between">
              <span>CPU:</span>
              <span>{{ systemMetrics.cpu_usage }}%</span>
            </div>
            <div class="flex justify-between">
              <span>Uptime:</span>
              <span>{{ systemMetrics.uptime }}</span>
            </div>
          </div>
        </div>
        <div class="bg-blue-50 p-4 rounded-lg">
          <h3 class="font-medium text-blue-900 mb-2">Elasticsearch</h3>
          <div class="space-y-1 text-sm">
            <div class="flex justify-between">
              <span>Status:</span>
              <span :class="realtimeMetrics.elasticsearch.status === 'online' ? 'text-green-600' : 'text-red-600'">
                {{ realtimeMetrics.elasticsearch.status }}
              </span>
            </div>
            <div class="flex justify-between">
              <span>Response:</span>
              <span>{{ realtimeMetrics.elasticsearch.response_time }}ms</span>
            </div>
            <div class="flex justify-between">
              <span>Documents:</span>
              <span>{{ realtimeMetrics.elasticsearch.document_count }}</span>
            </div>
          </div>
        </div>
        <div class="bg-orange-50 p-4 rounded-lg">
          <h3 class="font-medium text-orange-900 mb-2">Solr</h3>
          <div class="space-y-1 text-sm">
            <div class="flex justify-between">
              <span>Status:</span>
              <span :class="realtimeMetrics.solr.status === 'online' ? 'text-green-600' : 'text-red-600'">
                {{ realtimeMetrics.solr.status }}
              </span>
            </div>
            <div class="flex justify-between">
              <span>Response:</span>
              <span>{{ realtimeMetrics.solr.response_time }}ms</span>
            </div>
            <div class="flex justify-between">
              <span>Documents:</span>
              <span>{{ realtimeMetrics.solr.document_count }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Test Runner -->
    <div class="bg-white p-6 rounded-lg shadow-sm border mb-8">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">🧪 Run Benchmark Tests</h2>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Test Type</label>
          <select v-model="testConfig.type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="performance">Performance Test</option>
            <option value="vietnamese">Vietnamese Language Test</option>
            <option value="indexing">Indexing Test</option>
            <option value="all">All Tests</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Iterations</label>
          <input 
            v-model.number="testConfig.iterations" 
            type="number" 
            min="1" 
            max="100" 
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
        </div>
      </div>

      <div class="mt-6 flex space-x-4">
        <button 
          @click="runBenchmark" 
          :disabled="isRunning"
          class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
        >
          <svg v-if="isRunning" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ isRunning ? 'Running...' : 'Run Benchmark' }}
        </button>

        <button 
          @click="refreshData" 
          class="px-6 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 flex items-center"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          Refresh Data
        </button>
      </div>
    </div>

    <!-- Performance Comparison Chart -->
    <div class="bg-white p-6 rounded-lg shadow-sm border mb-8">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">📊 Performance Comparison</h2>
      <div class="h-64 flex items-end space-x-4">
        <div class="flex flex-col items-center flex-1">
          <div 
            class="w-8 bg-blue-500 rounded-t transition-all duration-500"
            :style="{ height: `${elasticsearchBarHeight}px` }"
          ></div>
          <span class="text-xs text-gray-600 mt-2">Elasticsearch</span>
          <span class="text-xs text-gray-500">{{ stats.elasticsearch.avgResponseTime }}ms</span>
        </div>
        
        <div class="flex flex-col items-center flex-1">
          <div 
            class="w-8 bg-orange-500 rounded-t transition-all duration-500"
            :style="{ height: `${solrBarHeight}px` }"
          ></div>
          <span class="text-xs text-gray-600 mt-2">Solr</span>
          <span class="text-xs text-gray-500">{{ stats.solr.avgResponseTime }}ms</span>
        </div>
      </div>
    </div>

    <!-- Detailed Results Table -->
    <div class="bg-white p-6 rounded-lg shadow-sm border mb-8">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">📋 Detailed Results</h2>
      
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Engine</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Test Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Response Time</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Throughput</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Success Rate</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="result in recentResults" :key="result.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="result.engine === 'elasticsearch' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800'" 
                      class="px-2 py-1 text-xs font-semibold rounded-full">
                  {{ result.engine }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ result.test_name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ result.response_time_avg }}ms</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ result.throughput }} QPS</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ result.success_rate }}%</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(result.created_at) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Search Quality Metrics -->
    <div class="bg-white p-6 rounded-lg shadow-sm border">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">🎯 Search Quality Metrics</h2>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <h3 class="text-lg font-medium text-gray-900 mb-3">Elasticsearch</h3>
          <div class="space-y-2">
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Precision:</span>
              <span class="text-sm font-medium">{{ qualityMetrics.elasticsearch.precision }}%</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Recall:</span>
              <span class="text-sm font-medium">{{ qualityMetrics.elasticsearch.recall }}%</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">F1 Score:</span>
              <span class="text-sm font-medium">{{ qualityMetrics.elasticsearch.f1_score }}%</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Vietnamese Score:</span>
              <span class="text-sm font-medium">{{ qualityMetrics.elasticsearch.vietnamese_score }}%</span>
            </div>
          </div>
        </div>

        <div>
          <h3 class="text-lg font-medium text-gray-900 mb-3">Apache Solr</h3>
          <div class="space-y-2">
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Precision:</span>
              <span class="text-sm font-medium">{{ qualityMetrics.solr.precision }}%</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Recall:</span>
              <span class="text-sm font-medium">{{ qualityMetrics.solr.recall }}%</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">F1 Score:</span>
              <span class="text-sm font-medium">{{ qualityMetrics.solr.f1_score }}%</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Vietnamese Score:</span>
              <span class="text-sm font-medium">{{ qualityMetrics.solr.vietnamese_score }}%</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import axios from 'axios'
import DualSearchTest from '@/components/DualSearchTest.vue'

// Reactive data
const stats = ref({
  elasticsearch: { 
    avgResponseTime: 0,
    minResponseTime: 0,
    maxResponseTime: 0,
    consistency: 0,
    avgResults: 0
  },
  solr: { 
    avgResponseTime: 0,
    minResponseTime: 0,
    maxResponseTime: 0,
    consistency: 0,
    avgResults: 0
  },
  overall: { successRate: 0, winner: 'N/A' }
})

const qualityMetrics = ref({
  elasticsearch: { precision: 0, recall: 0, f1_score: 0, vietnamese_score: 0 },
  solr: { precision: 0, recall: 0, f1_score: 0, vietnamese_score: 0 },
  overall: { precision: 0, recall: 0, f1_score: 0, vietnamese_score: 0 }
})

const resourceStats = ref({
  elasticsearch: { memory: 0, cpu: 0, disk: 0, network: 0 },
  solr: { memory: 0, cpu: 0, disk: 0, network: 0 },
  overall: { memory: 0, cpu: 0, disk: 0, network: 0 }
})

const recentResults = ref([])
const isRunning = ref(false)

const testConfig = ref({
  type: 'performance',
  iterations: 5
})

// Chart data - now using ChartWrapper components

const popularKeywords = ref([
  { term: 'iPhone 15', count: 45 },
  { term: 'MacBook Pro', count: 32 },
  { term: 'sách giáo khoa', count: 28 },
  { term: 'laptop cũ', count: 25 },
  { term: 'máy tính bảng', count: 18 }
])

const trendsData = ref({
  labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
  elasticsearch: [28, 32, 25, 35, 30, 27, 29],
  solr: [26, 29, 23, 31, 28, 25, 27]
})

// Charts removed - using simple HTML/CSS instead

// Load testing
const loadTestConfig = ref({
  concurrentUsers: 10,
  requestsPerUser: 5,
  testDuration: 60,
  engine: 'both'
})

const loadTestResults = ref(null)
const isLoadTesting = ref(false)

// Real-time monitoring
const realtimeMetrics = ref({
  elasticsearch: {
    status: 'online',
    response_time: 0,
    document_count: 0
  },
  solr: {
    status: 'online',
    response_time: 0,
    document_count: 0
  }
})

const systemMetrics = ref({
  memory_usage: { percentage: 0 },
  cpu_usage: 0,
  uptime: '0d 0h 0m'
})

// Computed properties
const elasticsearchBarHeight = computed(() => {
  const max = Math.max(stats.value.elasticsearch.avgResponseTime, stats.value.solr.avgResponseTime)
  return max > 0 ? (stats.value.elasticsearch.avgResponseTime / max) * 200 : 0
})

const solrBarHeight = computed(() => {
  const max = Math.max(stats.value.elasticsearch.avgResponseTime, stats.value.solr.avgResponseTime)
  return max > 0 ? (stats.value.solr.avgResponseTime / max) * 200 : 0
})

// Methods
const fetchSummary = async () => {
  try {
    const response = await axios.get('/api/comparison/summary')
    if (response.data.success) {
      const data = response.data.data
      
      // Performance metrics
      stats.value = {
        elasticsearch: {
          avgResponseTime: Math.round(data.performance?.elasticsearch?.avg_response_time || 0),
          minResponseTime: Math.round(data.performance?.elasticsearch?.min_response_time || 0),
          maxResponseTime: Math.round(data.performance?.elasticsearch?.max_response_time || 0),
          consistency: Math.round(data.performance?.elasticsearch?.consistency || 0),
          avgResults: Math.round(data.performance?.elasticsearch?.avg_results || 0)
        },
        solr: {
          avgResponseTime: Math.round(data.performance?.solr?.avg_response_time || 0),
          minResponseTime: Math.round(data.performance?.solr?.min_response_time || 0),
          maxResponseTime: Math.round(data.performance?.solr?.max_response_time || 0),
          consistency: Math.round(data.performance?.solr?.consistency || 0),
          avgResults: Math.round(data.performance?.solr?.avg_results || 0)
        },
        overall: {
          successRate: data.performance?.elasticsearch?.success_rate || 0,
          winner: data.overall_winner || 'N/A'
        }
      }
      
      // Quality metrics
      qualityMetrics.value = {
        elasticsearch: {
          precision: Math.round((data.quality?.elasticsearch?.precision || 0) * 100),
          recall: Math.round((data.quality?.elasticsearch?.recall || 0) * 100),
          f1_score: Math.round((data.quality?.elasticsearch?.f1_score || 0) * 100),
          vietnamese_score: Math.round((data.quality?.elasticsearch?.vietnamese_score || 0) * 100)
        },
        solr: {
          precision: Math.round((data.quality?.solr?.precision || 0) * 100),
          recall: Math.round((data.quality?.solr?.recall || 0) * 100),
          f1_score: Math.round((data.quality?.solr?.f1_score || 0) * 100),
          vietnamese_score: Math.round((data.quality?.solr?.vietnamese_score || 0) * 100)
        },
        overall: {
          precision: Math.round(((data.quality?.elasticsearch?.precision || 0) + (data.quality?.solr?.precision || 0)) / 2 * 100),
          recall: Math.round(((data.quality?.elasticsearch?.recall || 0) + (data.quality?.solr?.recall || 0)) / 2 * 100),
          f1_score: Math.round(((data.quality?.elasticsearch?.f1_score || 0) + (data.quality?.solr?.f1_score || 0)) / 2 * 100),
          vietnamese_score: Math.round(((data.quality?.elasticsearch?.vietnamese_score || 0) + (data.quality?.solr?.vietnamese_score || 0)) / 2 * 100)
        }
      }
    }
  } catch (error) {
    console.error('Failed to fetch summary:', error)
  }
}

const fetchResourceStats = async () => {
  try {
    const response = await axios.get('/api/comparison/resources')
    if (response.data.success) {
      const data = response.data.data
      resourceStats.value = {
        elasticsearch: {
          memory: Math.round(data.elasticsearch?.memory?.value || 0),
          cpu: Math.round(data.elasticsearch?.cpu?.value || 0),
          disk: Math.round(data.elasticsearch?.disk?.value || 0),
          network: Math.round(data.elasticsearch?.network?.value || 0)
        },
        solr: {
          memory: Math.round(data.solr?.memory?.value || 0),
          cpu: Math.round(data.solr?.cpu?.value || 0),
          disk: Math.round(data.solr?.disk?.value || 0),
          network: Math.round(data.solr?.network?.value || 0)
        },
        overall: {
          memory: Math.round(((data.elasticsearch?.memory?.value || 0) + (data.solr?.memory?.value || 0)) / 2),
          cpu: Math.round(((data.elasticsearch?.cpu?.value || 0) + (data.solr?.cpu?.value || 0)) / 2),
          disk: Math.round(((data.elasticsearch?.disk?.value || 0) + (data.solr?.disk?.value || 0)) / 2),
          network: Math.round(((data.elasticsearch?.network?.value || 0) + (data.solr?.network?.value || 0)) / 2)
        }
      }
    }
  } catch (error) {
    console.error('Failed to fetch resource stats:', error)
  }
}

const fetchRecentResults = async () => {
  try {
    const response = await axios.get('/api/comparison/recent')
    if (response.data.success) {
      recentResults.value = response.data.data
    }
  } catch (error) {
    console.error('Failed to fetch recent results:', error)
  }
}

const runBenchmark = async () => {
  isRunning.value = true
  try {
    const response = await axios.post('/api/comparison/test', {
      test_type: testConfig.value.type,
      options: {
        iterations: testConfig.value.iterations
      }
    })
    
    if (response.data.success) {
      // Refresh data after benchmark
      await Promise.all([fetchSummary(), fetchRecentResults()])
    }
  } catch (error) {
    console.error('Failed to run benchmark:', error)
  } finally {
    isRunning.value = false
  }
}

const refreshData = async () => {
  await Promise.all([fetchSummary(), fetchResourceStats(), fetchRecentResults()])
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString()
}

// Charts are now handled by ChartWrapper components

// Load testing methods
const runLoadTest = async () => {
  isLoadTesting.value = true
  try {
    const response = await axios.post('/api/load-test/run', {
      concurrent_users: loadTestConfig.value.concurrentUsers,
      requests_per_user: loadTestConfig.value.requestsPerUser,
      test_duration: loadTestConfig.value.testDuration,
      engine: loadTestConfig.value.engine,
      keywords: ['iPhone 15', 'MacBook Pro', 'sách giáo khoa', 'laptop cũ', 'máy tính bảng']
    })
    
    if (response.data.success) {
      loadTestResults.value = response.data.results
    }
  } catch (error) {
    console.error('Load test failed:', error)
  } finally {
    isLoadTesting.value = false
  }
}

// Real-time monitoring methods
const fetchRealtimeMetrics = async () => {
  try {
    const response = await axios.get('/api/monitor/realtime')
    if (response.data.success) {
      const data = response.data.data
      realtimeMetrics.value = {
        elasticsearch: data.elasticsearch,
        solr: data.solr
      }
      systemMetrics.value = data.system
    }
  } catch (error) {
    console.error('Failed to fetch real-time metrics:', error)
  }
}

// Lifecycle
onMounted(async () => {
  await refreshData()
  await fetchRealtimeMetrics()
  
  // Set up real-time monitoring interval
  setInterval(fetchRealtimeMetrics, 5000) // Update every 5 seconds
})
</script>

<style scoped>
.comparison-dashboard {
  padding: 1.5rem;
  background-color: #f9fafb;
  min-height: 100vh;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
  .comparison-dashboard {
    padding: 1rem;
  }
  
  .grid {
    gap: 1rem;
  }
  
  .text-2xl {
    font-size: 1.5rem;
  }
  
  .text-xl {
    font-size: 1.25rem;
  }
  
  .p-6 {
    padding: 1rem;
  }
  
  .h-64 {
    height: 12rem;
  }
  
  .h-48 {
    height: 10rem;
  }
  
  .h-32 {
    height: 8rem;
  }
}

@media (max-width: 640px) {
  .comparison-dashboard {
    padding: 0.5rem;
  }
  
  .grid-cols-1 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }
  
  .lg\\:grid-cols-2 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }
  
  .lg\\:grid-cols-3 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }
  
  .text-sm {
    font-size: 0.75rem;
  }
  
  .text-xs {
    font-size: 0.625rem;
  }
}

/* Animation for bars */
.bg-blue-500, .bg-orange-500 {
  transition: height 0.5s ease-in-out;
}

/* Hover effects */
.hover\:bg-gray-50:hover {
  background-color: #f9fafb;
}
</style>
