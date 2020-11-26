import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:rebloc/rebloc.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/enums/selected-tab.dart';
import 'package:resold/models/product.dart';
import 'package:resold/models/vendor.dart';
import 'package:resold/screens/tabs/map.dart';
import 'package:resold/screens/tabs/sell.dart';
import 'package:resold/screens/tabs/account.dart';
import 'package:resold/screens/tabs/orders.dart';
import 'package:resold/screens/tabs/search.dart';
import 'package:resold/screens/messages/inbox.dart';
import 'package:resold/services/firebase.dart';
import 'package:resold/state/actions/set-selected-tab.dart';
import 'package:resold/state/app-state.dart';
import 'package:resold/state/map-state.dart';
import 'package:resold/state/search-state.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';

class Home extends StatelessWidget {
  // This widget is the root of your application.
  @override
  Widget build(BuildContext context) {
    return ViewModelSubscriber<AppState, CustomerResponse>(
        converter: (state) => state.customer,
        builder: (context, dispatcher, customer) => MaterialApp(
            title: 'Resold',
            theme: ThemeData(
                primarySwatch: const MaterialColor(0xff41b8ea, {
                  50: Color.fromRGBO(25, 72, 92, .1),
                  100: Color.fromRGBO(25, 72, 92, .2),
                  200: Color.fromRGBO(25, 72, 92, .3),
                  300: Color.fromRGBO(25, 72, 92, .4),
                  400: Color.fromRGBO(25, 72, 92, .5),
                  500: Color.fromRGBO(25, 72, 92, .6),
                  600: Color.fromRGBO(25, 72, 92, .7),
                  700: Color.fromRGBO(25, 72, 92, .8),
                  800: Color.fromRGBO(25, 72, 92, .9),
                  900: Color.fromRGBO(25, 72, 92, 1)
                }),
                sliderTheme: SliderThemeData(
                    valueIndicatorColor: ResoldBlue,
                    showValueIndicator: ShowValueIndicator.never,
                    valueIndicatorTextStyle: TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.bold,
                    ),
                    tickMarkShape: SliderTickMarkShape.noTickMark),
                scaffoldBackgroundColor: Colors.white,
                brightness: Brightness.light,
                accentColor: Colors.white,
                primaryColor: ResoldBlue,
                splashColor: ResoldBlue,
                backgroundColor: Colors.white),
            home: HomePage(customer)));
  } // end function build
} // end class Home

class HomePageState extends State<HomePage> {
  final CustomerResponse customer;

  HomePageState(this.customer);

  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return ViewModelSubscriber<AppState, int>(
        converter: (state) => state.selectedTab.index,
        builder: (context, dispatcher, selectedTab) {
          return Scaffold(
            appBar: AppBar(
              title: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Align(
                      alignment: Alignment.centerLeft,
                      child: Image.asset('assets/images/resold-white-logo.png', width: 145, height: 145)),
                  Align(
                    alignment: Alignment.centerRight,
                    child: InkWell(
                      child: StreamBuilder(
                          stream: Firebase.getUnreadMessageCount(customer.id),
                          builder: (context, snapshot) {
                            if (snapshot.hasData && snapshot.data.documents.length != 0) {
                              return Stack(
                                children: <Widget>[
                                  Icon(Icons.message, color: Colors.white),
                                  new Positioned(
                                    right: 0,
                                    child: new Container(
                                        padding: EdgeInsets.all(1),
                                        decoration: new BoxDecoration(
                                          color: Colors.red,
                                          borderRadius: BorderRadius.circular(6),
                                        ),
                                        constraints: BoxConstraints(
                                          minWidth: 12,
                                          minHeight: 12,
                                        ),
                                        child: Text(
                                          '${snapshot.data.documents.length}',
                                          style: new TextStyle(
                                            color: Colors.white,
                                            fontSize: 8,
                                          ),
                                          textAlign: TextAlign.center,
                                        )),
                                  )
                                ],
                              );
                            } else {
                              return Icon(Icons.message, color: Colors.white);
                            } // end if we have unread message count
                          }),
                      onTap: () {
                        Navigator.push(context, MaterialPageRoute(builder: (context) => InboxPage(customer)));
                      },
                    ),
                    // child: Icon(Icons.message, color: Colors.white),
                  )
                ],
              ),
              iconTheme: IconThemeData(
                color: Colors.white, //change your color here
              ),
              backgroundColor: ResoldBlue,
            ),
            body: Center(
              child: getContent(context, SelectedTab.values[selectedTab]),
            ),
            bottomNavigationBar: BottomNavigationBar(
              type: BottomNavigationBarType.fixed,
              items: <BottomNavigationBarItem>[
                BottomNavigationBarItem(icon: Icon(Icons.home), label: 'Home'),
                BottomNavigationBarItem(icon: Icon(Icons.map), label: 'Map'),
                BottomNavigationBarItem(icon: Icon(Icons.attach_money), label: 'Sell'),
                BottomNavigationBarItem(icon: Icon(MdiIcons.truck), label: 'Deliveries'),
                BottomNavigationBarItem(icon: Icon(Icons.person), label: 'Account')
              ],
              currentIndex: selectedTab,
              backgroundColor: Colors.white,
              fixedColor: ResoldBlue,
              unselectedItemColor: Colors.black,
              onTap: (int index) => dispatcher(SetSelectedTabAction(SelectedTab.values[index])),
            ),
          );
        } // end selected tab builder
        );
  } // end function build

  Widget getContent(BuildContext context, SelectedTab selectedTab) {
    switch (selectedTab) {
      case SelectedTab.home:
      case SelectedTab.map:
        return ViewModelSubscriber<AppState, CustomerResponse>(
            converter: (state) => state.customer,
            builder: (context, dispatcher, customer) {
              return ViewModelSubscriber<AppState, Position>(
                  converter: (state) => state.currentLocation,
                  builder: (context, dispatcher, currentLocation) {
                    return ViewModelSubscriber<AppState, SearchState>(
                        converter: (state) => state.searchState,
                        builder: (context, dispatcher, searchState) {
                          return StreamBuilder<List<Product>>(
                              initialData: searchState.initialProducts,
                              stream: searchState.searchStream.stream,
                              builder: (context, snapshot) {
                                List<Product> results = snapshot.hasData ? snapshot.data : [];
                                if (selectedTab == SelectedTab.map) {
                                  return ViewModelSubscriber<AppState, MapState>(
                                      converter: (state) => state.mapState,
                                      builder: (context, dispatcher, mapState) {
                                        return MapPage(
                                            customer: customer,
                                            searchState: searchState,
                                            results: results,
                                            currentLocation: currentLocation,
                                            pinPillPosition: mapState.pillPosition,
                                            selectedProduct: mapState.selectedProduct,
                                            dispatcher: dispatcher);
                                      });
                                } else {
                                  return SearchPage(
                                      customer: customer,
                                      searchState: searchState,
                                      results: results,
                                      currentLocation: currentLocation,
                                      dispatcher: dispatcher);
                                } // end if map tab
                              });
                        });
                  });
            });
      case SelectedTab.sell:
        return ViewModelSubscriber<AppState, CustomerResponse>(
            converter: (state) => state.customer,
            builder: (context, dispatcher, customer) {
              return ViewModelSubscriber<AppState, Position>(
                  converter: (state) => state.currentLocation,
                  builder: (context, dispatcher, currentLocation) {
                    return SellPage(customer: customer, currentLocation: currentLocation, dispatcher: dispatcher);
                  });
            });
      case SelectedTab.orders:
        return ViewModelSubscriber<AppState, CustomerResponse>(
            converter: (state) => state.customer,
            builder: (context, dispatcher, customer) {
              return OrdersPage(customer: customer);
            });
      case SelectedTab.account:
        return ViewModelSubscriber<AppState, CustomerResponse>(
            converter: (state) => state.customer,
            builder: (context, dispatcher, customer) {
              return ViewModelSubscriber<AppState, Vendor>(
                  converter: (state) => state.vendor,
                  builder: (context, dispatcher, vendor) {
                    return ViewModelSubscriber<AppState, Position>(
                        converter: (state) => state.currentLocation,
                        builder: (context, dispatcher, currentLocation) {
                          return ViewModelSubscriber<AppState, List<Product>>(
                              converter: (state) => state.forSaleProducts,
                              builder: (context, dispatcher, forSaleProducts) {
                                return ViewModelSubscriber<AppState, List<Product>>(
                                    converter: (state) => state.soldProducts,
                                    builder: (context, dispatcher, soldProducts) {
                                      return AccountPage(
                                          customer: customer,
                                          vendor: vendor,
                                          currentLocation: currentLocation,
                                          forSaleProducts: forSaleProducts,
                                          soldProducts: soldProducts);
                                    });
                              });
                        });
                  });
            });
      default:
        return Text('Unknown tab');
    } // end switch on selected tab
  } // end function getContent

} // end class HomePageState

class HomePage extends StatefulWidget {
  final CustomerResponse customer;

  HomePage(this.customer, {Key key}) : super(key: key);

  @override
  HomePageState createState() => HomePageState(this.customer);
} // end class HomePage
