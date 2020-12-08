import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:flutter/scheduler.dart';
import 'package:geolocator/geolocator.dart';
import 'package:intl/intl.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/constants/url-config.dart';
import 'package:resold/models/product.dart';
import 'package:resold/screens/product/view.dart';
import 'package:resold/state/search-state.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/widgets/loading.dart';
import 'package:resold/widgets/location/distance.dart';
import 'creation-aware-list-item.dart';

class ProductList extends StatelessWidget {
  final CustomerResponse customer;
  final SearchState searchState;
  final List<Product> products;
  final Position currentLocation;
  final Function dispatcher;
  final Function handleItemCreated;

  ProductList(
      {this.customer, this.searchState, this.products, this.currentLocation, this.handleItemCreated, this.dispatcher});

  @override
  Widget build(BuildContext context) {
    return ListView.builder(
        itemCount: products.length,
        itemBuilder: (context, index) {
          return CreationAwareListItem(
              itemCreated: () {
                SchedulerBinding.instance.addPostFrameCallback((duration) => handleItemCreated(index));
              },
              child: buildProductListTile(context, currentLocation, products[index], customer, dispatcher, index));
        });
  } // end function build

  static Widget buildProductListTile(BuildContext context, Position currentLocation, Product product,
      CustomerResponse customer, Function dispatcher, int index) {
    var formatter = new NumberFormat("\$###,###", "en_US");
    return product.name == LoadingIndicatorTitle
        ? Center(child: Loading())
        : ListTile(
            title: Card(
                child: InkWell(
                    splashColor: Colors.blue.withAlpha(30),
                    onTap: () {
                      Navigator.push(
                          context,
                          MaterialPageRoute(
                              builder: (context) => ProductPage(
                                  customer: customer,
                                  currentLocation: currentLocation,
                                  product: product,
                                  dispatcher: dispatcher)));
                    },
                    child: Container(
                        decoration: BoxDecoration(color: Colors.white),
                        child: Container(
                            padding: EdgeInsets.fromLTRB(25, 25, 25, 25),
                            child: Column(
                              children: [
                                Row(children: [
                                  Column(children: [
                                    Align(
                                        alignment: Alignment.center,
                                        child: SizedBox(
                                            height: 270,
                                            width: 270,
                                            child: CachedNetworkImage(
                                              placeholder: (context, url) => Container(
                                                child: Loading(),
                                                width: 200.0,
                                                height: 200.0,
                                                padding: EdgeInsets.all(70.0),
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
                                            ))),
                                    SizedBox(height: 5),
                                  ])
                                ]),
                                Row(
                                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                                        Container(
                                          padding: new EdgeInsets.only(right: 13.0),
                                          width: 200,
                                          child: new Text(
                                            product.name,
                                            overflow: TextOverflow.fade,
                                            style: new TextStyle(
                                              fontSize: 14.0,
                                              fontFamily: 'Roboto',
                                              fontWeight: FontWeight.normal,
                                            ),
                                          ),
                                        ),
                                        SizedBox(height: 5),
                                        Text(formatter.format(double.parse(product.price).round()),
                                            style: new TextStyle(
                                              fontSize: 12.0,
                                              fontFamily: 'Roboto',
                                              fontWeight: FontWeight.bold,
                                            ))
                                      ]),
                                      Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                                        Container(
                                            width: 70,
                                            child: Align(
                                                alignment: Alignment.centerRight,
                                                child: Distance(
                                                  startLatitude: currentLocation.latitude,
                                                  startLongitude: currentLocation.longitude,
                                                  endLatitude: product.latitude,
                                                  endLongitude: product.longitude,
                                                )))
                                      ])
                                    ])
                              ],
                            ))))));
  } // end function buildProductListTile
}
