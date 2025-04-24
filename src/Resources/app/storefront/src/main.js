import PudoPlugin from './pudo-plugin/pudo-plugin';

const PluginManager = window.PluginManager;

PluginManager.register('PudoPlugin', PudoPlugin, '[pudo-plugin]');
