

import Alpine from 'alpinejs';

import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';
import collapse from '@alpinejs/collapse'

Alpine.plugin(collapse)

window.Alpine = Alpine;
window.Cropper = Cropper;

Alpine.start();