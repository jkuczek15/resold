import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:resold/widgets/loading.dart';

class Distance extends StatelessWidget {
  final double startLatitude;
  final double startLongitude;
  final double endLatitude;
  final double endLongitude;

  Distance({this.startLatitude, this.startLongitude, this.endLatitude, this.endLongitude});

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<double>(
      future: Geolocator().distanceBetween(startLatitude, startLongitude, endLatitude, endLongitude),
      initialData: 0.0,
      builder: (context, snapshot) {
        if (snapshot.hasData) {
          var miles = (snapshot.data / 1609.344).toStringAsFixed(1);
          return Text("$miles mi",
              overflow: TextOverflow.fade,
              style: new TextStyle(
                fontSize: 14.0,
                fontFamily: 'Raleway',
                fontWeight: FontWeight.normal,
              ));
        } else {
          return Center(
            child: Loading(),
          );
        }
      },
    );
  } // end function build
}
