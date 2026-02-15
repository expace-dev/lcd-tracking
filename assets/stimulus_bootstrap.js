import { startStimulusApp } from '@symfony/stimulus-bridge';
import LightboxController from './controllers/lightbox_controller.js';
import InfinitePagerController from './controllers/infinite_pager_controller.js';

// Registers Stimulus controllers from controllers.json and in the controllers/ directory
export const app = startStimulusApp(require.context(
  '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
  true,
  /\.[jt]sx?$/
));

// register any custom, 3rd party controllers here
app.register('lightbox', LightboxController);
app.register('infinite-pager', InfinitePagerController);