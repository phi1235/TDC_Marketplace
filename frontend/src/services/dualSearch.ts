import axios from 'axios'
import type { Listing } from './listings'

export interface DualSearchResult {
  elasticsearch: {
    data: Listing[]
    count: number
    response_time: number
    engine: string
  }
  solr: {
    data: Listing[]
    count: number
    response_time: number
    engine: string
  }
  comparison: {
    es_time: number
    solr_time: number
    winner: string
    time_difference: number
  }
  timestamp: string
}

export interface SearchAnalytics {
  query: string
  timestamp: string
  elasticsearch: {
    response_time: number
    result_count: number
    success: boolean
  }
  solr: {
    response_time: number
    result_count: number
    success: boolean
  }
  winner: string
  user_id?: number
}

class DualSearchService {
  private analytics: SearchAnalytics[] = []

  /**
   * 🔍 Thực hiện tìm kiếm song song trên cả Elasticsearch và Solr
   */
  async performDualSearch(query: string): Promise<DualSearchResult> {
    const startTime = Date.now()
    
    try {
      // Chạy cả hai search engines song song
      const [esResponse, solrResponse] = await Promise.allSettled([
        axios.get('/api/search-es', { params: { q: query } }),
        axios.get('/api/search-solr', { params: { q: query } })
      ])

      const esResult = esResponse.status === 'fulfilled' ? esResponse.value.data : {
        data: [],
        count: 0,
        response_time: 0,
        engine: 'elasticsearch'
      }

      const solrResult = solrResponse.status === 'fulfilled' ? solrResponse.value.data : {
        data: [],
        count: 0,
        response_time: 0,
        engine: 'solr'
      }

      // So sánh hiệu năng
      const winner = esResult.response_time < solrResult.response_time ? 'elasticsearch' : 'solr'
      const timeDifference = Math.abs(esResult.response_time - solrResult.response_time)

      const result: DualSearchResult = {
        elasticsearch: esResult,
        solr: solrResult,
        comparison: {
          es_time: esResult.response_time,
          solr_time: solrResult.response_time,
          winner,
          time_difference: timeDifference
        },
        timestamp: new Date().toISOString()
      }

      // Lưu analytics
      this.saveSearchAnalytics(query, result)

      return result
    } catch (error) {
      console.error('Dual search failed:', error)
      throw error
    }
  }

  /**
   * 📊 Lưu thông tin analytics của search
   */
  private saveSearchAnalytics(query: string, result: DualSearchResult) {
    const analytics: SearchAnalytics = {
      query,
      timestamp: result.timestamp,
      elasticsearch: {
        response_time: result.elasticsearch.response_time,
        result_count: result.elasticsearch.count,
        success: result.elasticsearch.count >= 0
      },
      solr: {
        response_time: result.solr.response_time,
        result_count: result.solr.count,
        success: result.solr.count >= 0
      },
      winner: result.comparison.winner
    }

    this.analytics.push(analytics)

    // Giữ tối đa 100 search analytics
    if (this.analytics.length > 100) {
      this.analytics = this.analytics.slice(-100)
    }

    // Gửi analytics lên server (optional)
    this.sendAnalyticsToServer(analytics)
  }

  /**
   * 📤 Gửi analytics lên server
   */
  private async sendAnalyticsToServer(analytics: SearchAnalytics) {
    try {
      await axios.post('/api/search-analytics', analytics)
    } catch (error) {
      console.warn('Failed to send analytics to server:', error)
    }
  }

  /**
   * 📈 Lấy thống kê search
   */
  getSearchAnalytics(): SearchAnalytics[] {
    return this.analytics
  }

  /**
   * 🏆 Lấy engine thắng nhiều nhất
   */
  getWinningEngine(): string {
    const esWins = this.analytics.filter(a => a.winner === 'elasticsearch').length
    const solrWins = this.analytics.filter(a => a.winner === 'solr').length
    
    if (esWins > solrWins) return 'elasticsearch'
    if (solrWins > esWins) return 'solr'
    return 'tie'
  }

  /**
   * ⚡ Lấy thời gian phản hồi trung bình
   */
  getAverageResponseTime(): { elasticsearch: number, solr: number } {
    if (this.analytics.length === 0) {
      return { elasticsearch: 0, solr: 0 }
    }

    const esAvg = this.analytics.reduce((sum, a) => sum + a.elasticsearch.response_time, 0) / this.analytics.length
    const solrAvg = this.analytics.reduce((sum, a) => sum + a.solr.response_time, 0) / this.analytics.length

    return {
      elasticsearch: Math.round(esAvg * 100) / 100,
      solr: Math.round(solrAvg * 100) / 100
    }
  }

  /**
   * 🔄 Kiểm tra trạng thái của cả hai engines
   */
  async checkEnginesStatus(): Promise<{ elasticsearch: boolean, solr: boolean }> {
    try {
      const [esStatus, solrStatus] = await Promise.allSettled([
        axios.get('/api/search-es', { params: { q: 'test' } }),
        axios.get('/api/search-solr', { params: { q: 'test' } })
      ])

      return {
        elasticsearch: esStatus.status === 'fulfilled',
        solr: solrStatus.status === 'fulfilled'
      }
    } catch (error) {
      return { elasticsearch: false, solr: false }
    }
  }
}

export const dualSearchService = new DualSearchService()
