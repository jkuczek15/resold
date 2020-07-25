import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/view_models/product-view-model.dart';
import 'package:resold/models/product.dart';
import 'package:resold/widgets/creation-aware-list-item.dart';
import 'package:provider/provider.dart';
import 'package:flutter/scheduler.dart';
import 'package:intl/intl.dart';
import 'package:geolocator/geolocator.dart';

class ProductListBuilder {

  static String baseImagePath = 'https://s3-us-west-2.amazonaws.com/resold-photos/catalog/product';

  static ChangeNotifierProvider<ProductViewModel> buildProductList(List<Object> data, Position currentLocation) {
    return ChangeNotifierProvider<ProductViewModel> (
      create: (_) => new ProductViewModel(currentLocation, data),
      child: Consumer<ProductViewModel> (
        builder: (context, model, child) => ListView.builder(
          itemCount: model.items.length,
          itemBuilder: (context, index) {
            if(index == 0) {
              return Column(
                children: [
                  SizedBox(height: 10),
                  SingleChildScrollView(
                      scrollDirection: Axis.horizontal,
                      child: Row(
                          children: <Widget>[
                            Padding(
                              padding: EdgeInsets.fromLTRB(10, 10, 10, 10),
                              child: Image.asset('assets/images/categories/electronics.jpg', height: 175)
                            ),
                            Padding(
                                padding: EdgeInsets.fromLTRB(10, 10, 10, 10),
                                child: Image.asset('assets/images/categories/electronics.jpg', height: 175)
                            ),
                            Padding(
                                padding: EdgeInsets.fromLTRB(10, 10, 10, 10),
                                child: Image.asset('assets/images/categories/electronics.jpg', height: 175)
                            ),
                            Padding(
                                padding: EdgeInsets.fromLTRB(10, 10, 10, 10),
                                child: Image.asset('assets/images/categories/electronics.jpg', height: 175)
                            ),
                          ]
                      )
                  )
                ]
              );
            }
            index -= 1;
            return CreationAwareListItem(
              itemCreated: () {
                SchedulerBinding.instance.addPostFrameCallback((duration) => model.handleItemCreated(index));
              },
              child: model.items[index+1].name == LoadingIndicatorTitle ?
              Center(child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea)))
                  : buildProductTile(currentLocation, model.items[index], index)
            );
          }
        ),
      ),
    );
  }

  static ListTile buildProductTile(Position currentLocation, Product data, int index) {
    var formatter = new NumberFormat("\$###,###", "en_US");
    return ListTile(
        title: Card(
          child: InkWell(
              splashColor: Colors.blue.withAlpha(30),
              onTap: () { /* ... */ },
              child: Container(
                decoration: BoxDecoration(color: Colors.white),
                child: Container (
                  padding: EdgeInsets.fromLTRB(25, 25, 25, 25),
                  child: Column (
                      children: [
                        Row (
                            children: [
                              Column(
                                  children: [
                                    Align(
                                        alignment: Alignment.center,
                                        child: SizedBox (
                                            height: 270,
                                            width: 270,
                                            child: FadeInImage(image: NetworkImage(baseImagePath + data.thumbnail), placeholder: AssetImage('assets/images/placeholder-image.png'), fit: BoxFit.cover)
                                        )
                                    ),
                                    SizedBox(height: 5),
                                  ]
                              )
                            ]
                        ),
                        Row (
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Container(
                                      padding: new EdgeInsets.only(right: 13.0),
                                      width: 200,
                                      child: new Text(
                                        data.name,
                                        overflow: TextOverflow.fade,
                                        style: new TextStyle(
                                          fontSize: 14.0,
                                          fontFamily: 'Roboto',
                                          fontWeight: FontWeight.normal,
                                        ),
                                      ),
                                    ),
                                    SizedBox(height: 5),
                                    Text(formatter.format(double.parse(data.price).round()),
                                      style: new TextStyle(
                                        fontSize: 12.0,
                                        fontFamily: 'Roboto',
                                        fontWeight: FontWeight.bold,
                                      )
                                    )
                                  ]
                              ),
                              Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Container(
                                      width: 70,
                                      child: Align(
                                        alignment: Alignment.centerRight,
                                        child: calculateDistance(currentLocation.latitude, currentLocation.longitude, data.latitude, data.longitude)
                                      )
                                    )
                                  ]
                              )
                            ]
                        )
                  ],
                )
            )
          )
        )
      )
    );
  }

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
          return Text("${miles} mi", overflow: TextOverflow.fade, style: new TextStyle(
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

