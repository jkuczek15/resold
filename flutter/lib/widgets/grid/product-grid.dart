import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:resold/constants/url-config.dart';
import 'package:resold/models/product.dart';
import 'package:resold/screens/product/view.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/widgets/loading.dart';

class ProductGrid extends StatelessWidget {
  final CustomerResponse customer;
  final List<Product> products;
  final Position currentLocation;
  final Function dispatcher;

  ProductGrid({this.customer, this.products, this.currentLocation, this.dispatcher});

  @override
  Widget build(BuildContext context) {
    return GridView.count(
        physics: ScrollPhysics(),
        shrinkWrap: true,
        crossAxisCount: 2,
        children: List.generate(products.length,
            (index) => buildProductGridTile(context, currentLocation, products[index], customer, dispatcher, index)));
  } // end function build

  static Widget buildProductGridTile(BuildContext context, Position currentLocation, Product product,
      CustomerResponse customer, Function dispatcher, int index) {
    return Card(
        elevation: 3,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(30),
        ),
        margin: EdgeInsets.fromLTRB(10, 5, 10, 5),
        child: InkWell(
            splashColor: Colors.blue.withAlpha(30),
            onTap: () {
              Navigator.push(context,
                  MaterialPageRoute(builder: (context) => ProductPage(customer, currentLocation, product, dispatcher)));
            },
            child: CachedNetworkImage(
              placeholder: (context, url) => Container(
                child: Loading(),
                width: 200.0,
                height: 200.0,
                decoration: BoxDecoration(
                  color: Colors.blueGrey,
                  borderRadius: BorderRadius.all(
                    Radius.circular(8.0),
                  ),
                ),
              ),
              errorWidget: (context, url, error) => Material(
                child: Image.asset(
                  'assets/images/placeholder-image.png',
                  width: 200.0,
                  height: 200.0,
                  fit: BoxFit.cover,
                ),
                borderRadius: BorderRadius.all(
                  Radius.circular(8.0),
                ),
                clipBehavior: Clip.hardEdge,
              ),
              imageUrl: baseProductImagePath + product.thumbnail,
              width: 200.0,
              height: 200.0,
              fit: BoxFit.cover,
            )));
  } // end function buildProductGridTile
}
