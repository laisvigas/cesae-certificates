import './bootstrap';

import Alpine from 'alpinejs';
import { addParticipantForm } from './add-participant'
import { tipsWidget } from './tips'
import { registerEventCreate } from './event-create' 

window.Alpine = Alpine;
window.addParticipantForm = addParticipantForm
Alpine.data('tipsWidget', tipsWidget)

registerEventCreate(Alpine) 
Alpine.start();
