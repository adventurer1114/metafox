/* eslint-disable react-hooks/rules-of-hooks */
import useScript from './useScript';
import useGlobal from './useGlobal';

export default function useGoogleMap() {
  const { getSetting } = useGlobal();

  const key = getSetting('core.google.google_map_api_key');

  return useScript(
    `https://maps.googleapis.com/maps/api/js?key=${key}&libraries=maps,places`
  );
}
