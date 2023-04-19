import { useTheme } from '@mui/styles';
import { debounce } from 'lodash';
import React, { useEffect } from 'react';
import { BasicPlaceItem } from '../types';
import useGoogleMap from './useGoogleMap';

export default function useSearchPlaces(
  mapContainerRef: React.MutableRefObject<HTMLDivElement>,
  center: Partial<BasicPlaceItem>
): [
  BasicPlaceItem[],
  string,
  React.Dispatch<React.SetStateAction<string>>,
  BasicPlaceItem,
  (item: BasicPlaceItem) => void,
  (location: BasicPlaceItem, callback: (data: any) => void) => void,
  Record<string, any>
] {
  const statusGoogleMap = useGoogleMap();
  const readyGoogleMap = statusGoogleMap === 'ready';
  const [items, setItems] = React.useState<BasicPlaceItem[]>([]);
  const [meta, setMeta] = React.useState({ loading: false, error: false });

  const [currentItem, setCurrentItem] = React.useState<BasicPlaceItem>();
  const [query, setQuery] = React.useState<string>(center?.address);

  const mapObjectRef = React.useRef<google.maps.Map>();
  const serviceRef = React.useRef<google.maps.places.PlacesService>();
  const geocoderService = React.useRef<google.maps.places.GeocoderService>();
  const theme = useTheme();
  const mounted = React.useRef(null);

  const initMap = ({ lat, lng }: BasicPlaceItem) => {
    mapObjectRef.current = new google.maps.Map(mapContainerRef.current, {
      zoom: 15,
      center: {
        lat,
        lng
      },
      backgroundColor: theme.palette.background.default
    });

    serviceRef.current = new google.maps.places.PlacesService(
      mapObjectRef.current
    );
    geocoderService.current = new google.maps.Geocoder();

    center?.name &&
      serviceRef.current.textSearch(
        {
          query: center?.address || center.name,
          location: { lat: center.lat, lng: center.lng }
        },
        (results, status) => {
          if ('OK' === status && mounted.current) {
            setCurrentItem({
              name: results[0].name,
              address: results[0].formatted_address,
              lat: results[0].geometry.location.lat(),
              lng: results[0].geometry.location.lng()
            });
          }
        }
      );

    serviceRef.current.textSearch(
      { types: ['locality'], query: undefined },
      (results, status) => {
        if ('OK' === status && mounted.current) {
          setItems(
            results.map((x: any) => ({
              icon: x.icon,
              name: x.name,
              address: x.formatted_address,
              lat: x.geometry.location.lat(),
              lng: x.geometry.location.lng()
            }))
          );
        } else {
          setMeta(meta => ({ ...meta, error: results }));
        }

        setMeta(meta => ({ ...meta, loading: false }));
      }
    );
  };

  const initService = React.useCallback(() => {
    if (!mapContainerRef.current || serviceRef.current || !readyGoogleMap)
      return;

    setMeta(meta => ({ ...meta, loading: true }));

    navigator.geolocation.getCurrentPosition(
      (position: GeolocationPosition) => {
        initMap({
          lat: center?.lat || position.coords.latitude,
          lng: center?.lng || position.coords.longitude
        });
      },
      () => {
        initMap({
          lat: center?.lat || 0,
          lng: center?.lng || 0
        });
      }
    );

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [mapContainerRef, readyGoogleMap]);

  const setCenter = React.useCallback((item: { lat: number; lng: number }) => {
    if (mapObjectRef.current) {
      mapObjectRef.current.setCenter({ lat: item.lat, lng: item.lng });
    }
  }, []);

  const searchPlaces = React.useMemo(() => {
    function searchPlacesFn(criteria: any) {
      if (!serviceRef.current) {
        return;
      }

      setMeta(meta => ({ ...meta, loading: true }));
      serviceRef.current.textSearch(criteria, (results, status) => {
        if ('OK' === status && mounted.current) {
          setItems(
            results.map((x: any) => ({
              icon: x.icon,
              name: x.name,
              address: x.formatted_address,
              lat: x.geometry.location.lat(),
              lng: x.geometry.location.lng()
            }))
          );
        } else {
          setMeta(meta => ({ ...meta, error: results }));
        }

        setMeta(meta => ({ ...meta, loading: false }));
      });
    }

    return debounce(searchPlacesFn, 200);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const getDetailPlace = React.useCallback((criteria, callback) => {
    if (!serviceRef.current || !geocoderService.current) {
      return;
    }

    geocoderService.current.geocode(
      { location: criteria },
      (results, status) => {
        if ('OK' === status && mounted.current) {
          callback(results[results.length - 1]);
        }
      }
    );
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  // avoid setState when component unmounted
  useEffect(() => {
    mounted.current = true;

    return () => {
      mounted.current = false;
    };
  }, []);

  React.useEffect(() => {
    setImmediate(() => initService());

    if (!query) return searchPlaces({ query: undefined, types: ['locality'] });

    searchPlaces({ query });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [query, readyGoogleMap]);

  return [items, query, setQuery, currentItem, setCenter, getDetailPlace, meta];
}
