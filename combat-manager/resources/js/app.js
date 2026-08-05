import Alpine from 'alpinejs';
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';
import collapse from '@alpinejs/collapse';

import npcBuilder from './npc-builder/index.js';

Alpine.plugin(collapse);

window.Alpine = Alpine;
window.Cropper = Cropper;

document.addEventListener('alpine:init', () => {
    Alpine.data('npcBuilder', npcBuilder);
});

Alpine.start();