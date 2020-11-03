import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:liquid_pull_to_refresh/liquid_pull_to_refresh.dart';
import 'package:geolocator/geolocator.dart';
import 'package:resold/services/search.dart';
import 'package:resold/models/product.dart';
import 'package:resold/builders/product-list-builder.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/widgets/loading.dart';

class BrowsePage extends StatefulWidget {
  final CustomerResponse customer;

  BrowsePage(CustomerResponse customer, {Key key}) : customer = customer, super(key: key);

  @override
  BrowsePageState createState() => BrowsePageState(customer);
}

class BrowsePageState extends State<BrowsePage> {

  CustomerResponse customer;
  Position currentLocation;
  Future<List<Product>> futureLocalProducts;

  BrowsePageState(CustomerResponse customer) : customer = customer;

  @override
  void initState() {
    super.initState();
    Geolocator().getCurrentPosition(desiredAccuracy: LocationAccuracy.high).then((location) {
      if(this.mounted) {
        setState(() {
          currentLocation = location;
          futureLocalProducts = Search.fetchLocalProducts(location.latitude, location.longitude);
        });
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return LiquidPullToRefresh(
      height: 80,
      springAnimationDurationInMilliseconds: 500,
      onRefresh: () => futureLocalProducts = Search.fetchLocalProducts(currentLocation.latitude, currentLocation.longitude),
      showChildOpacityTransition: false,
      color: const Color(0xff41b8ea),
      animSpeedFactor: 5.0,
      child: FutureBuilder<List<Product>>(
        future: futureLocalProducts,
        builder: (context, snapshot) {
          if (snapshot.hasData && currentLocation != null) {
            return ProductListBuilder.buildProductList(context, snapshot.data, currentLocation, customer, true);
          } else if (snapshot.hasError) {
            return Text("${snapshot.error}");
          }
          // By default, show a loading spinner.
          return Center(child: Loading());
        },
      )
    );
  }
}
