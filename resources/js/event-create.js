export function registerEventCreate(Alpine){
  Alpine.data('eventComposer', eventComposer)
  Alpine.data('dateLinker', dateLinker)
  Alpine.data('bannerPicker', bannerPicker)
  Alpine.data('descriptionCounter', descriptionCounter)
}

function eventComposer(){
  return {
    state: { title: '', start: '', end: '', typeLabel: '', bannerName: '', hours: '' },
    init(){
      // valores iniciais dos inputs
      this.state.title  = document.querySelector('input[name=title]')?.value || ''
      this.state.start  = document.querySelector('input[name=start_at]')?.value || ''
      this.state.end    = document.querySelector('input[name=end_at]')?.value || ''
      this.state.hours  = document.querySelector('input[name=hours]')?.value || ''

      const type = document.querySelector('select[name=event_type_id]')
      this.state.typeLabel = type?.selectedOptions?.[0]?.text?.trim() || ''

      // escuta mudanças dos inputs
      document.addEventListener('input', (ev)=>{
        const el = ev.target
        if(el.name === 'title')         this.state.title = el.value
        if(el.name === 'start_at')      this.state.start = el.value
        if(el.name === 'end_at')        this.state.end   = el.value
        if(el.name === 'event_type_id') this.state.typeLabel = el.selectedOptions?.[0]?.text?.trim() || ''
        if(el.name === 'hours')         this.state.hours = el.value
      })

      document.addEventListener('banner-selected', (e)=>{ this.state.bannerName = e.detail })

      document.addEventListener('submit-event-form', (e)=>{
        const draft = e?.detail?.draft
        const form = document.getElementById('event-form')
        if(!form) return
        if(draft){
          const i = document.createElement('input'); i.type='hidden'; i.name='is_draft'; i.value='1'
          form.appendChild(i)
        }
        form.requestSubmit()
      })
    },
    dateRange(){
      if(!this.state.start && !this.state.end) return '—'
      const fmt = (v)=> v?.replace('T',' ') || ''
      return `${fmt(this.state.start)} — ${fmt(this.state.end)}`
    },
    // usa somente o campo "hours"
    duration(){
      if(!this.state.hours) return ''  
      return `${this.state.hours}h`
    }
  }
}


function dateLinker(){
  return {
    init(){
      const root = this.$root
      const s = root.querySelector('input[name=start_at]')
      const e = root.querySelector('input[name=end_at]')

      const update = ()=>{
        if(s?.value){ e.min = s.value }
        if(e?.value && s?.value && new Date(e.value) < new Date(s.value)){
          e.value = s.value
        }
      }
      s?.addEventListener('change', update)
      e?.addEventListener('change', update)
      update()
    }
  }
}


function descriptionCounter({ initial = '' }={}){
  return {
    txt: initial,
    max: 600,
  }
}

function bannerPicker(){
  return {
    fileName: '',
    onPick(ev){
      const f = ev.target.files?.[0]
      if(!f) return
      this.fileName = f.name
      this.$dispatch('banner-selected', this.fileName)
    }
  }
}