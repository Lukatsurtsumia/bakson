import {
    Alpine,
    Livewire,
} from '../../vendor/livewire/livewire/dist/livewire.esm';
import beforeAfterGallery from './components/beforeAfterGallery';
import beforeAfterSlider from './components/beforeAfterSlider';
import heroSlideshow from './components/heroSlideshow';
import mobileActionBar from './components/mobileActionBar';
import siteHeader from './components/siteHeader';

// Livewire ships Alpine; registering the site's components on that same instance lets Blade and
// Livewire views share them.
Alpine.data('beforeAfterGallery', beforeAfterGallery);
Alpine.data('beforeAfterSlider', beforeAfterSlider);
Alpine.data('heroSlideshow', heroSlideshow);
Alpine.data('mobileActionBar', mobileActionBar);
Alpine.data('siteHeader', siteHeader);

Livewire.start();
