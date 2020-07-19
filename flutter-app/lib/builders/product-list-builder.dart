import 'package:flutter/material.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/view_models/product-view-model.dart';
import 'package:resold/models/product.dart';
import 'package:resold/widgets/creation-aware-list-item.dart';
import 'package:provider/provider.dart';
import 'package:flutter/scheduler.dart';

class ProductListBuilder {

  static String baseImagePath = 'https://s3-us-west-2.amazonaws.com/resold-photos/catalog/product';

  static ChangeNotifierProvider<ProductViewModel> buildProductList(List<Object> data) {
    return ChangeNotifierProvider<ProductViewModel> (
      create: (_) => new ProductViewModel(data),
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
              child: model.items[index].name == LoadingIndicatorTitle ? Center(child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea))) : buildProductTile(model.items[index], index)
            );
          }
        ),
      ),
    );
  }

  static ListTile buildProductTile(Product data, int index) {
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
                                              height: 300,
                                              width: 300,
                                              child: FadeInImage(image: NetworkImage(baseImagePath + data.thumbnail), placeholder: AssetImage('assets/images/placeholder-image.png'), fit: BoxFit.contain)
                                          )
                                      ),
                                      SizedBox(height: 5),
                                    ]
                                )
                              ]
                          ),
                          Row (
                              children: [
                                Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Container(
                                        padding: new EdgeInsets.only(right: 13.0),
                                        width: 250,
                                        child: new Text(
                                          data.name,
                                          overflow: TextOverflow.fade,
                                          style: new TextStyle(
                                            fontSize: 16.0,
                                            fontFamily: 'Roboto',
                                            fontWeight: FontWeight.normal,
                                          ),
                                        ),
                                      ),
                                      SizedBox(height: 5),
                                      Text("\$" + double.parse(data.price).round().toString(),
                                        style: new TextStyle(
                                          fontSize: 13.0,
                                          fontFamily: 'Roboto',
                                          fontWeight: FontWeight.bold,
                                        )
                                      )
                                    ]
                                )
                              ]
                          )
                        ]
                    )
                ),
              )
          ),
        )
    );
  }
}

