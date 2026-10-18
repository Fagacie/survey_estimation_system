import { createElement } from 'react';
import { createRoot } from 'react-dom/client';
import { flushSync } from 'react-dom';
import MapLayerControl from './ises/components/map/MapLayerControl';
import BasemapControl from './ises/components/map/BasemapControl';

type Metric = { id: string; label: string; fallback: string };
const metrics: Metric[] = [
    { id: 'stat-boundary-area', label: 'Area', fallback: '0 m2' },
    { id: 'stat-main-count', label: 'Main lines', fallback: '0' },
    { id: 'stat-cross-count', label: 'Cross lines', fallback: '0' },
    { id: 'stat-total-nm', label: 'Total distance', fallback: '0.00 NM' },
];

function MapViewportSummary() {
    return (
        <div className="map-viewport-summary" aria-label="Live survey summary">
            {metrics.map((metric) => (
                <div className="map-viewport-summary-item" key={metric.id}>
                    <span>{metric.label}</span>
                    <strong id={'map-summary-' + metric.id} aria-live="polite">{metric.fallback}</strong>
                </div>
            ))}
        </div>
    );
}

const layerMount = document.getElementById('map-layer-control');
if (layerMount) {
    const root = createRoot(layerMount);
    flushSync(() => root.render(createElement(MapLayerControl)));
}

const basemapMount = document.getElementById('map-basemap-root');
if (basemapMount) {
    const root = createRoot(basemapMount);
    flushSync(() => root.render(createElement(BasemapControl)));
}

const summaryMount = document.getElementById('map-summary-root');
if (summaryMount && !(window as any).isDroneMode) {
    const root = createRoot(summaryMount);
    flushSync(() => root.render(createElement(MapViewportSummary)));
}
