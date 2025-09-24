import './bootstrap';

import Alpine from 'alpinejs';
import { addParticipantForm } from './add-participant'

window.Alpine = Alpine;
window.addParticipantForm = addParticipantForm

Alpine.start();
