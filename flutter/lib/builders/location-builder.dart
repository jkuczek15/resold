import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';

class LocationBuilder {

  static FutureBuilder <double> calculateDistance (double startLatitude, double startLongitude, endLatitude, endLongitude) {
    try {
      endLatitude = double.parse(endLatitude);
      endLongitude = double.parse(endLongitude);
    } catch (exception) {
      endLatitude = endLongitude = 0.0;
    }

    return FutureBuilder<double>(
      future: Geolocator().distanceBetween(startLatitude, startLongitude, endLatitude, endLongitude),
      initialData: 0.0,
      builder: (context, snapshot) {
        if (snapshot.hasData) {
          var miles = (snapshot.data / 1609.344).toStringAsFixed(1);
          return Text("$miles mi", overflow: TextOverflow.fade, style: new TextStyle(
            fontSize: 14.0,
            fontFamily: 'Roboto',
            fontWeight: FontWeight.normal,
          ));
        } else {
          return Center(
            child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea)),
          );
        }
      },
    );
  }
}
