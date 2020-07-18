import 'package:flutter/material.dart';
import '../models/product.dart';
import '../widgets/creation-aware-list-item.dart';

class ProductListBuilder {

  static String baseImagePath = 'https://s3-us-west-2.amazonaws.com/resold-photos/catalog/product';

  static ListView buildProductList(List<Product> data) {
    return ListView.builder(
      itemCount: data.length,
      itemBuilder: (context, index) {
        return buildProductTile(data[index], index);
      },
    );
  }

  static CreationAwareListItem buildProductTile(Product data, int index) {
    return CreationAwareListItem(
      itemCreated: () {
        print('Item created at $index');
      },
      child: ListTile(
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
                                          width: 200,
                                          child: new Text(
                                            data.name,
                                            overflow: TextOverflow.fade,
                                            style: new TextStyle(
                                              fontSize: 13.0,
                                              fontFamily: 'Roboto',
                                              fontWeight: FontWeight.bold,
                                            ),
                                          ),
                                        ),
                                        Text("\$" + double.parse(data.price).round().toString())
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
      )
    );
  }
}