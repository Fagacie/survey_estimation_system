import { useState } from 'react';
import { Map, Satellite, Waves, Moon } from 'lucide-react';

type Basemap = { id: string; label: string; Icon: typeof Map };
const options: Basemap[] = [
    { id: 'osm', label: 'Street', Icon: Map },
    { id: 'satellite', label: 'Satellite', Icon: Satellite },
    { id: 'ocean', label: 'Ocean', Icon: Waves },
    { id: 'dark', label: 'Dark', Icon: Moon },
];

/** Basemap UI bridge; the existing Leaflet engine remains responsible for tile layers. */
export default function BasemapControl() {
    const [active, setActive] = useState('osm');

    const choose = (id: string) => {
        if (!options.some((option) => option.id === id)) return;
        document.dispatchEvent(new CustomEvent('ises:basemap-change', { detail: { basemap: id } }));
        setActive(id);
    };

    return (
        <div className="map-basemap-control" role="group" aria-label="Map style">
            {options.map(({ id, label, Icon }) => (
                <button key={id} type="button" className={'map-basemap-btn' + (active === id ? ' active' : '')}
                    aria-pressed={active === id} onClick={() => choose(id)}>
                    <Icon size={13} aria-hidden="true" />
                    <span>{label}</span>
                </button>
            ))}
        </div>
    );
}
