import 'package:flutter/widgets.dart';
import 'package:resold/view-models/firebase/firebase-delivery-quote.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/widgets/list/delivery-quote-list.dart';

class DeliveryQuoteWidgetBuilder {
  static Widget buildDeliveryQuoteWidget(
      CustomerResponse customer, List<FirebaseDeliveryQuote> quotes, Function dispatcher,
      {String error}) {
    if (quotes.length == 0) {
      return Center(child: Text(error));
    } // end if no deliveries

    return SingleChildScrollView(
        child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: <Widget>[
        Container(
            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          if (quotes.length > 0) DeliveryQuoteList(customer: customer, quotes: quotes, dispatcher: dispatcher),
        ]))
      ],
    ));
  } // end function buildOrdersWidget
}
