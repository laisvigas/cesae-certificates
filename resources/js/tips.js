import tips from '../data/tips.json'

// util: escolhe um item aleatório diferente do anterior
function pickRandom(arr, lastIndex = -1) {
  if (!arr.length) return { index: -1, item: null }
  let idx = Math.floor(Math.random() * arr.length)
  if (arr.length > 1 && idx === lastIndex) idx = (idx + 1) % arr.length
  return { index: idx, item: arr[idx] }
}

export function tipsWidget() {
  return {
    tips,
    idx: -1,
    tip: { text: '', type: '' },
    storageKey: 'dashboard:lastTipIdx',

    iconFor(type) {
      const map = { 
        health: '💧', 
        focus: '🧠', 
        productivity: '⚙️', 
        mindset: '🌱' , 
        curiosity: '🔬',
        portugal: '🇵🇹'
    }
      return map[type] || '✨'
    },

    loadLast() {
      const saved = localStorage.getItem(this.storageKey)
      return saved ? parseInt(saved, 10) : -1
    },

    save(idx) { localStorage.setItem(this.storageKey, String(idx)) },

    next() {
      const { index, item } = pickRandom(this.tips, this.idx)
      this.idx = index
      this.tip = item || { text: 'Tenha um ótimo dia! ✨', type: 'mindset' }
      if (index >= 0) this.save(index)
    },

    init() {
      const last = this.loadLast()
      const { index, item } = pickRandom(this.tips, last)
      this.idx = index
      this.tip = item || { text: 'Respire. Você está indo bem. ✨', type: 'mindset' }
      if (index >= 0) this.save(index)
    }
  }
}
