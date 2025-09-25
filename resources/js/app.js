import './bootstrap';

import Alpine from 'alpinejs';
import { addParticipantForm } from './add-participant'
import { tipsWidget } from './tips'

window.Alpine = Alpine;
window.addParticipantForm = addParticipantForm
Alpine.data('tipsWidget', tipsWidget)

Alpine.start();
