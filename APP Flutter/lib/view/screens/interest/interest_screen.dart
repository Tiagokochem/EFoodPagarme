import 'package:efood_multivendor/controller/category_controller.dart';
import 'package:efood_multivendor/controller/splash_controller.dart';
import 'package:efood_multivendor/helper/responsive_helper.dart';
import 'package:efood_multivendor/helper/route_helper.dart';
import 'package:efood_multivendor/util/dimensions.dart';
import 'package:efood_multivendor/util/styles.dart';
import 'package:efood_multivendor/view/base/custom_button.dart';
import 'package:efood_multivendor/view/base/custom_image.dart';
import 'package:efood_multivendor/view/base/no_data_screen.dart';
import 'package:efood_multivendor/view/base/web_menu_bar.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';

class InterestScreen extends StatefulWidget {
  const InterestScreen({Key? key}) : super(key: key);

  @override
  State<InterestScreen> createState() => _InterestScreenState();
}

class _InterestScreenState extends State<InterestScreen> {

  @override
  void initState() {
    super.initState();

    Get.find<CategoryController>().getCategoryList(true);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: ResponsiveHelper.isDesktop(context) ? const WebMenuBar() : null,
      body: SafeArea(
        child: GetBuilder<CategoryController>(builder: (categoryController) {
          return categoryController.categoryList != null ? categoryController.categoryList!.isNotEmpty ? Center(
            child: Container(
              width: Dimensions.webMaxWidth,
              padding: const EdgeInsets.all(Dimensions.paddingSizeSmall),
              child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                const SizedBox(height: Dimensions.paddingSizeLarge),

                Text('choose_your_interests'.tr, style: robotoMedium.copyWith(fontSize: 22)),
                const SizedBox(height: Dimensions.paddingSizeSmall),

                Text('get_personalized_recommendations'.tr, style: robotoRegular.copyWith(color: Theme.of(context).disabledColor)),
                const SizedBox(height: Dimensions.paddingSizeLarge),

                Expanded(
                  child: GridView.builder(
                    physics: const BouncingScrollPhysics(),
                    itemCount: categoryController.categoryList!.length,
                    gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: ResponsiveHelper.isDesktop(context) ? 4 : ResponsiveHelper.isTab(context) ? 3 : 2,
                      childAspectRatio: (1/0.35),
                    ),
                    itemBuilder: (context, index) {
                      return InkWell(
                        onTap: () => categoryController.addInterestSelection(index),
                        child: Container(
                          margin: const EdgeInsets.all(Dimensions.paddingSizeExtraSmall),
                          padding: const EdgeInsets.symmetric(
                            vertical: Dimensions.paddingSizeExtraSmall, horizontal: Dimensions.paddingSizeSmall,
                          ),
                          decoration: BoxDecoration(
                            color: categoryController.interestSelectedList![index] ? Theme.of(context).primaryColor
                                : Theme.of(context).cardColor,
                            borderRadius: BorderRadius.circular(Dimensions.radiusSmall),
                            boxShadow: [BoxShadow(color: Colors.grey[Get.isDarkMode ? 800 : 200]!, blurRadius: 5, spreadRadius: 1)],
                          ),
                          alignment: Alignment.center,
                          child: Row(children: [
                            CustomImage(
                              image: '${Get.find<SplashController>().configModel!.baseUrls!.categoryImageUrl}'
                                  '/${categoryController.categoryList![index].image}',
                              height: 30, width: 30,
                            ),
                            const SizedBox(width: Dimensions.paddingSizeExtraSmall),
                            Flexible(child: Text(
                              categoryController.categoryList![index].name!,
                              style: robotoMedium.copyWith(
                                fontSize: Dimensions.fontSizeSmall,
                                color: categoryController.interestSelectedList![index] ? Theme.of(context).cardColor
                                    : Theme.of(context).textTheme.bodyLarge!.color,
                              ),
                              maxLines: 1, overflow: TextOverflow.ellipsis,
                            )),
                          ]),
                        ),
                      );
                    },
                  ),
                ),

                !categoryController.isLoading ? CustomButton(
                  buttonText: 'save_and_continue'.tr,
                  onPressed: () {
                    List<int?> interests = [];
                    for(int index=0; index<categoryController.categoryList!.length; index++) {
                      if(categoryController.interestSelectedList![index]) {
                        interests.add(categoryController.categoryList![index].id);
                      }
                    }
                    categoryController.saveInterest(interests).then((isSuccess) {
                      if(isSuccess) {
                        Get.offAllNamed(RouteHelper.getInitialRoute());
                      }
                    });
                  },
                ) : const Center(child: CircularProgressIndicator()),

              ]),
            ),
          ) : NoDataScreen(text: 'no_category_found'.tr) : const Center(child: CircularProgressIndicator());
        }),
      ),
    );
  }
}
