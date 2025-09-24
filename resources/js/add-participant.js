export function addParticipantForm(lookupUrl) {
  return {
    email: '',
    fields: { name:'', phone:'', address:'', document_type:'', document_number:'', nationality:'' },
    found: false,
    isLocked: true,
    updateExisting: false,
    submitting: false,
    statusMessage: '',
    status: 'idle',

    statusClass() {
      return {
        'text-gray-500': this.status === 'idle' || this.status === 'checking',
        'text-green-600': this.status === 'found',
        'text-blue-600': this.status === 'not_found',
        'text-red-600': this.status === 'error',
      };
    },

    canSubmitNew() { return (this.fields.name || '').trim().length > 0; },
    toggleLock() { this.isLocked = !this.updateExisting; },

    async maybeLookup() {
      if (this.email.includes('@') && this.email.includes('.')) await this.lookup();
    },

    async lookup() {
      const email = (this.email || '').trim().toLowerCase();
      if (!email) return;
      this.status = 'checking';
      this.statusMessage = 'A verificar email...';
      try {
        const res = await fetch(`${lookupUrl}?email=${encodeURIComponent(email)}`, { headers: { 'Accept': 'application/json' }});
        if (!res.ok) throw new Error('Falha na verificação');
        const data = await res.json();

        if (data.found) {
          this.found = true; this.isLocked = true; this.updateExisting = false;
          this.fields = {
            name: data.participant.name ?? '',
            phone: data.participant.phone ?? '',
            address: data.participant.address ?? '',
            document_type: data.participant.document_type ?? '',
            document_number: data.participant.document_number ?? '',
            nationality: data.participant.nationality ?? '',
          };
          this.status = 'found';
          this.statusMessage = 'Participante já existe. Pode anexar diretamente ou marcar "Atualizar dados existentes".';
        } else {
          this.found = false; this.isLocked = false; this.updateExisting = false;
          this.status = 'not_found';
          this.statusMessage = 'Novo participante. Preencha os campos e salve.';
        }
      } catch (e) {
        console.error(e);
        this.status = 'error';
        this.statusMessage = 'Erro ao verificar o email.';
      }
    },

    init() {
      // (Opcional) debounce via watcher:
      let t;
      this.$watch('email', (val) => {
        clearTimeout(t);
        if (!val) return;
        t = setTimeout(() => this.maybeLookup(), 400);
      });
    }
  }
}
