import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:geolocator/geolocator.dart';
import 'package:rebloc/rebloc.dart';
import 'package:resold/state/app-state.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/widgets/loading.dart';

class MapPage extends StatefulWidget {
  MapPage({Key key}) : super(key: key);

  @override
  MapPageState createState() => MapPageState();
}

class MapPageState extends State<MapPage> {
  Future<Position> futureCurrentLocation;
  final Map<String, Marker> markers = {};

  @override
  void initState() {
    super.initState();
    futureCurrentLocation = Geolocator().getCurrentPosition(desiredAccuracy: LocationAccuracy.high);
  } // end function initState

  @override
  Widget build(BuildContext context) {
    return ViewModelSubscriber<AppState, CustomerResponse>(
        converter: (state) => state.customer,
        builder: (context, dispatcher, model) {
          return FutureBuilder<Position>(
              future: futureCurrentLocation,
              builder: (context, snapshot) {
                if (snapshot.hasData) {
                  return Container(
                      height: 600,
                      child: GoogleMap(
                        onMapCreated: (GoogleMapController controller) => onMapCreated(controller, snapshot.data),
                        initialCameraPosition: CameraPosition(
                          target: LatLng(snapshot.data.latitude, snapshot.data.longitude),
                          zoom: 9.0,
                        ),
                        markers: markers.values.toSet(),
                      ));
                } else {
                  return Center(child: Loading());
                } // end if snapshot has data
              });
        });
  } // end function build

  Future<void> onMapCreated(GoogleMapController controller, Position currentLocation) async {
    setState(() {
      markers.clear();

      final String currentLocationTitle = "You";
      final currentLocationMarker = Marker(
        markerId: MarkerId(currentLocationTitle),
        position: LatLng(currentLocation.latitude, currentLocation.longitude),
        icon: BitmapDescriptor.defaultMarkerWithHue(198),
        infoWindow: InfoWindow(
          title: currentLocationTitle,
        ),
      );

      markers[currentLocationTitle] = currentLocationMarker;
    });
  } // end function onMapCreated
}
