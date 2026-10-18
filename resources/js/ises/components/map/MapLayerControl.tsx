import { useState } from 'react';
import { ChevronDown, ChevronUp, Layers3, Map, Satellite, Waves, Moon } from 'lucide-react';

type LayerOption = { id: string; label: string; color: string; checked: boolean };
type BasemapOption = { id: string; label: string };

const basemaps: BasemapOption[] = [
    { id: 'osm', label: 'Street' },
    { id: 'satellite', label: 'Satellite' },
    { id: 'ocean', label: 'Ocean' },
    { id: 'dark', label: 'Dark' },
];
const initialLayers: LayerOption[] = [
    { id: 'layer-boundaries', label: 'Boundaries', color: '#198754', checked: true },
    { id: 'layer-main-lines', label: 'Main lines', color: '#2563eb', checked: true },
    { id: 'layer-cross-lines', label: 'Cross lines', color: '#d97706', checked: true },
    { id: 'layer-labels', label: 'Line labels', color: '#64748b', checked: false },
];

/** React island for map layer visibility. IDs are intentionally stable for the existing Leaflet engine. */
export default function MapLayerControl() {
    const [expanded, setExpanded] = useState(true);
    const [layers, setLayers] = useState(initialLayers);

    const toggleLayer = (id: string, checked: boolean) => {
        setLayers((current) => current.map((item) => item.id === id ? { ...item, checked } : item));
    };

    return (
        <>
            <button type="button" className="map-layer-heading" id="btn-toggle-map-layers"
                aria-expanded={expanded} aria-controls="map-layer-options" onClick={() => setExpanded((value) => !value)}>
                <span className="map-layer-heading-title"><Layers3 size={14} aria-hidden="true" /> Feature layers</span>
                {expanded ? <ChevronUp size={14} aria-hidden="true" /> : <ChevronDown size={14} aria-hidden="true" />}
            </button>
            <div className="map-layer-options" id="map-layer-options" hidden={!expanded}>
                {layers.map((layer) => (
                    <label className="map-layer-option" key={layer.id} htmlFor={layer.id}>
                        <span className="map-layer-option-name">
                            <span className="map-layer-swatch" style={{ background: layer.color }} />
                            {layer.label}
                        </span>
                        <input id={layer.id} type="checkbox" role="switch" checked={layer.checked}
                            onChange={(event) => toggleLayer(layer.id, event.target.checked)} />
                    </label>
                ))}
            </div>
        </>
    );
}
