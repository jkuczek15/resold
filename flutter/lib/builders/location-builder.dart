import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:resold/widgets/loading.dart';

class LocationBuilder {

  static FutureBuilder <double> calculateDistance (double startLatitude, double startLongitude, double endLatitude, double endLongitude) {
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
            child: Loading(),
          );
        }
      },
    );
  }
}
