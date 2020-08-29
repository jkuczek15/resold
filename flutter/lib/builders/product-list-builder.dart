import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/constants/url-config.dart';
import 'package:resold/view-models/product-view-model.dart';
import 'package:resold/view-models/response/customer-response.dart';
import 'package:resold/models/product.dart';
import 'package:resold/widgets/list/creation-aware-list-item.dart';
import 'package:resold/screens/product/view.dart';
import 'package:resold/builders/location-builder.dart';
import 'package:provider/provider.dart';
import 'package:flutter/scheduler.dart';
import 'package:intl/intl.dart';
import 'package:geolocator/geolocator.dart';
import 'package:resold/widgets/scroll/scrollable-category-list.dart';

class ProductListBuilder {

  static ChangeNotifierProvider<ProductViewModel> buildProductList(
      BuildContext context, List<Product> products, Position currentLocation, CustomerResponse customer, bool showCategoryHeader) {
    return ChangeNotifierProvider<ProductViewModel>(
        create: (_) => new ProductViewModel(currentLocation, products),
        child: Consumer<ProductViewModel>(
            builder: (context, model, child) =>
                ListView.builder(
                    itemCount: model.items.length,
                    itemBuilder: (context, index) {
                      if (index == 0) {
                        return showCategoryHeader ? ScrollableCategoryList() : Column();
                      }
                      index -= 1;
                      return CreationAwareListItem(
                          itemCreated: () {
                            SchedulerBinding.instance.addPostFrameCallback((
                                duration) => model.handleItemCreated(index));
                          },
                          child: model.items[index + 1].name ==
                              LoadingIndicatorTitle
                              ?
                          Center(child: CircularProgressIndicator(
                              backgroundColor: const Color(0xff41b8ea)))
                              : buildProductListTile(
                              context, currentLocation, model.items[index], customer,
                              index)
                      );
                    }
                )
        )
    );
  }

  static Widget buildProductListTile(BuildContext context,
      Position currentLocation, Product product, CustomerResponse customer, int index) {
    var formatter = new NumberFormat("\$###,###", "en_US");
    return ListTile(
        title: Card(
            child: InkWell(
                splashColor: Colors.blue.withAlpha(30),
                onTap: () {
                  Navigator.push(context, MaterialPageRoute(
                      builder: (context) =>
                          ProductPage(product, customer, currentLocation)));
                },
                child: Container(
                    decoration: BoxDecoration(color: Colors.white),
                    child: Container(
                        padding: EdgeInsets.fromLTRB(25, 25, 25, 25),
                        child: Column(
                          children: [
                            Row(
                                children: [
                                  Column(
                                      children: [
                                        Align(
                                            alignment: Alignment.center,
                                            child: SizedBox(
                                                height: 270,
                                                width: 270,
                                                child: FadeInImage(
                                                    image: NetworkImage(
                                                        baseProductImagePath +
                                                            product.thumbnail),
                                                    placeholder: AssetImage(
                                                        'assets/images/placeholder-image.png'),
                                                    fit: BoxFit.cover)
                                            )
                                        ),
                                        SizedBox(height: 5),
                                      ]
                                  )
                                ]
                            ),
                            Row(
                                mainAxisAlignment: MainAxisAlignment
                                    .spaceBetween,
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Column(
                                      crossAxisAlignment: CrossAxisAlignment
                                          .start,
                                      children: [
                                        Container(
                                          padding: new EdgeInsets.only(
                                              right: 13.0),
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
                                        Text(formatter.format(
                                            double.parse(product.price)
                                                .round()),
                                            style: new TextStyle(
                                              fontSize: 12.0,
                                              fontFamily: 'Roboto',
                                              fontWeight: FontWeight.bold,
                                            )
                                        )
                                      ]
                                  ),
                                  Column(
                                      crossAxisAlignment: CrossAxisAlignment
                                          .start,
                                      children: [
                                        Container(
                                            width: 70,
                                            child: Align(
                                                alignment: Alignment
                                                    .centerRight,
                                                child: LocationBuilder
                                                    .calculateDistance(
                                                    currentLocation.latitude,
                                                    currentLocation.longitude,
                                                    product.latitude,
                                                    product.longitude)
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

  static Widget buildProductGridTile(BuildContext context, Position currentLocation, Product product, CustomerResponse customer, int index) {
    return Card(
        child: InkWell(
          splashColor: Colors.blue.withAlpha(30),
          onTap: () {
            Navigator.push(context, MaterialPageRoute(builder: (context) => ProductPage(product, customer, currentLocation)));
          },
          child: FadeInImage(
              image: NetworkImage(baseProductImagePath + product.image),
              placeholder: AssetImage('assets/images/placeholder-image.png'),
              fit: BoxFit.cover
          )
      )
    );
  }
}
