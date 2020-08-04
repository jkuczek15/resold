import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:liquid_pull_to_refresh/liquid_pull_to_refresh.dart';
import 'package:geolocator/geolocator.dart';
import 'package:resold/services/resold-search.dart';
import 'package:resold/models/product.dart';
import 'package:resold/builders/product-list-builder.dart';

class BrowsePage extends StatefulWidget {
  final Position currentLocation;

  BrowsePage(Position currentLocation, {Key key}) : currentLocation = currentLocation, super(key: key);

  @override
  BrowsePageState createState() => BrowsePageState(currentLocation);
}

class BrowsePageState extends State<BrowsePage> {

  final Position currentLocation;
  Future<List<Product>> futureLocalProducts;

  BrowsePageState(Position currentLocation) : currentLocation = currentLocation;

  @override
  void initState() {
    super.initState();
    Geolocator().getCurrentPosition(desiredAccuracy: LocationAccuracy.high).then((location) {
      if(this.mounted) {
        setState(() {
          futureLocalProducts = ResoldSearch.fetchLocalProducts(location.latitude, location.longitude);
        });
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return LiquidPullToRefresh(
        height: 80,
        springAnimationDurationInMilliseconds: 500,
        onRefresh: () => futureLocalProducts = ResoldSearch.fetchLocalProducts(currentLocation.latitude, currentLocation.longitude),
        showChildOpacityTransition: false,
        color: const Color(0xff41b8ea),
        animSpeedFactor: 5.0,
        child: FutureBuilder<List<Product>>(
          future: futureLocalProducts,
          builder: (context, snapshot) {
            if (snapshot.hasData) {
              return ProductListBuilder.buildProductList(context, snapshot.data, currentLocation);
            } else if (snapshot.hasError) {
              return Text("${snapshot.error}");
            }
            // By default, show a loading spinner.
            return Center(child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea)));
          },
        )
    );
  }
}
