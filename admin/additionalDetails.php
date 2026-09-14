<?php 
$k = 0;
$addlArray = array();
$addlArray[$k] = new additionalDetails(178,'Payment Cards','cscan_payment_cards');
$addlArray[$k]->setData('Payment Card Offer Details');
$addlArray[$k]->setData('Application Type','ApplicationType',1,'SELECT ApplicationTypeName,ApplicationTypeID FROM cscan_application_type WHERE ApplicationTypeID',true,'ORDER BY ApplicationTypeSort');
//$addlArray[$k]->setData('Pre-Screen & Opt-Out Notice','OptOutFirmOffer',3,'',true);
//$addlArray[$k]->setData('Card Type','CardType',1,'SELECT CardTypeName,CardTypeID FROM cscan_card_type WHERE CardTypeID',true,'ORDER BY CardTypeSort');
$addlArray[$k]->setData('Card Network','CardNetwork',6,'SELECT CardTypeName,CardTypeID FROM cscan_card_type WHERE CardTypeID',true,'ORDER BY CardTypeSort');
$addlArray[$k]->setData('Primary Card Level','CardLevel',6,'SELECT CardLevelTypeName,CardLevelTypeID FROM cscan_cardlevel_type WHERE CardLevelTypeID',true,'ORDER BY CardLevelTypeSort');
$addlArray[$k]->setData('Secondary Card Level(s)','SecondaryCardLevel',6,'SELECT CardLevelTypeName,CardLevelTypeID FROM cscan_cardlevel_type WHERE CardLevelTypeID',false,'ORDER BY CardLevelTypeSort');
$addlArray[$k]->setData('Rewards Program','RewardsProgram',3,'',true);
$addlArray[$k]->setData('Rewards Program Emphasis','RewardsProgramEmphasis',6,'SELECT RewardTypeName,RewardTypeID FROM cscan_reward_type WHERE RewardTypeID',false,'ORDER BY RewardTypeSort');
$addlArray[$k]->setData('Rewards Rate','RewardsRate',0,'',false);
$addlArray[$k]->setData('Reloadable','Reloadable',3,'',false);
$addlArray[$k]->setData('Regular Pricing');
$addlArray[$k]->setData('Tier 1 Purchase Regular APR (%)','PurchaseRegularAPR',2,'',true);
$addlArray[$k]->setData('Tier 2 Purchase Regular APR (%)','Tier2PurchaseRegularAPR',2,'',true);
$addlArray[$k]->setData('Tier 3 Purchase Regular APR (%)','Tier3PurchaseRegularAPR',2,'',true);
$addlArray[$k]->setData('Purchase Regular APR (%) Detail','PurchaseRegularAPRDetail',5,'',false);
$addlArray[$k]->setData('Purchase Regular Rate Type','PurchaseRateType',1,'SELECT RateTypeName,RateTypeID FROM cscan_rate_type WHERE RateTypeID',true,'ORDER BY RateTypeSort');
$addlArray[$k]->setData('Tier 1 Balance Transfer Regular APR (%)','BalanceTransferRegularAPR',2,'',true);
$addlArray[$k]->setData('Tier 2 Balance Transfer Regular APR (%)','Tier2BalanceTransferRegularAPR',2,'',true);
$addlArray[$k]->setData('Tier 3 Balance Transfer Regular APR (%)','Tier3BalanceTransferRegularAPR',2,'',true);
$addlArray[$k]->setData('Balance Transfer Regular APR (%) Detail','BalanceTransferRegularAPRDetail',5,'',false);
$addlArray[$k]->setData('Balance Transfer Regular Rate Type','BalanceTransferRateType',1,'SELECT RateTypeName,RateTypeID FROM cscan_rate_type WHERE RateTypeID',true,'ORDER BY RateTypeSort'); 
$addlArray[$k]->setData('Tier 1 Cash Advance Regular APR (%)','CashAdvanceRegularAPR',2,'',true);
$addlArray[$k]->setData('Tier 2 Cash Advance Regular APR (%)','Tier2CashAdvanceRegularAPR',2,'',true);
$addlArray[$k]->setData('Tier 3 Cash Advance Regular APR (%)','Tier3CashAdvanceRegularAPR',2,'',true);
$addlArray[$k]->setData('Cash Advance Regular APR (%) Detail','CashAdvanceRegularAPRDetail',5,'',false);
$addlArray[$k]->setData('Cash Advance Regular Rate Type','CashAdvanceRateType',1,'SELECT RateTypeName,RateTypeID FROM cscan_rate_type WHERE RateTypeID',true,'ORDER BY RateTypeSort');
$addlArray[$k]->setData('Penalty APR (%)','PenaltyAPR',2,'',true);
$addlArray[$k]->setData('Penalty APR Details','PenaltyAPRDetails',5,'',true);

$addlArray[$k]->setData('Pricing: Fees & Limit');
$addlArray[$k]->setData('Tier 1 Annual Fee ($)','Tier1AnnualFee',4,'',true);
$addlArray[$k]->setData('Tier 2 Annual Fee ($)','Tier2AnnualFee',4,'',true);
$addlArray[$k]->setData('Tier 3 Annual Fee ($)','AnnualFee',4,'',true);
$addlArray[$k]->setData('Annual Fee ($) Detail','AnnualFeeDetail',5,'',false);
$addlArray[$k]->setData('Tier 1 Late Fee ($)','Tier1LateFee',4,'',true);
$addlArray[$k]->setData('Tier 2 Late Fee ($)','Tier2LateFee',4,'',true);
$addlArray[$k]->setData('Tier 3 Late Fee ($)','LateFee',4,'',true);
$addlArray[$k]->setData('Late Fee ($) Detail','LateFeeDetail',5,'',false);
$addlArray[$k]->setData('Tier 1 Overlimit Fee ($)','Tier1OverlimitFee',4,'',true);
$addlArray[$k]->setData('Tier 2 Overlimit Fee ($)','Tier2OverlimitFee',4,'',true);
$addlArray[$k]->setData('Tier 3 Overlimit Fee ($)','OverlimitFee',4,'',true);
$addlArray[$k]->setData('Overlimit Fee ($) Detail','OverlimitFeeDetail',5,'',false);
$addlArray[$k]->setData('Balance Transfer Usage Fee (%)','BalanceTransferUsageFee',2,'',true);
$addlArray[$k]->setData('Balance Transfer Minimum Fee ($)','BalanceTransferMinimumFee',4,'',true); 
$addlArray[$k]->setData('Balance Transfer Maximum Fee ($)','BalanceTransferMaximumFee',4,'',true);
$addlArray[$k]->setData('Cash Advance Usage Fee (%)','CashAdvanceUsageFee',2,'',true); 
$addlArray[$k]->setData('Cash Advance Minimum Fee ($)','CashAdvanceMinimumFee',4,'',true);
$addlArray[$k]->setData('Cash Advance Maximum Fee ($)','CashAdvanceMaximumFee',4,'',true);
$addlArray[$k]->setData('Minimum Card Limit ($)','MinimumCardLimit',4,'',true);
$addlArray[$k]->setData('Maximum Card Limit ($)','MaximumCardLimit',4,'',true);
$addlArray[$k]->setData('Introductory Monthly Maintenance Fee ($)','MonthlyMaintenanceFee',4,'',true);
$addlArray[$k]->setData('Standard Monthly Maintenance Fee ($)','StandardMonthlyMaintenanceFee',4,'',true);

$addlArray[$k]->setData('Introductory Pricing');
$addlArray[$k]->setData('Purchase Introductory APR (%)','PurchaseIntroductoryAPR',2,'',true);
$addlArray[$k]->setData('Purchase Introductory Period (Months)','PurchaseIntroductoryPeriod',4,'',true);
$addlArray[$k]->setData('Balance Transfer Introductory APR (%)','BalanceTransferIntroductoryAPR',2,'',true);
$addlArray[$k]->setData('Balance Transfer Introductory Period (Months)','BalanceTransferIntroductoryPeriod',4,'',true);
$addlArray[$k]->setData('Balance Transfer Introductory Usage Fee (%)','BalanceTransferIntroductoryUsageFee',2,'',true);
$addlArray[$k]->setData('Balance Transfer Introductory Minimum Fee ($)','BalanceTransferIntroductoryMinimumFee',4,'',true);
$addlArray[$k]->setData('Balance Transfer Introductory Maximum Fee ($)','BalanceTransferIntroductoryMaximumFee',4,'',true);
$addlArray[$k]->setData('Balance Transfer Introductory Fee Period (Months)','BalanceTransferIntroductoryFeePeriod',4,'',true);
$addlArray[$k]->setData('Balance Transfer Introductory Fee Detail','BalanceTransferIntroductoryFeeDetail',5,'',false);
$addlArray[$k]->setData('Cash Advance Introductory APR (%)','CashAdvanceIntroductoryAPR',2,'',true);
$addlArray[$k]->setData('Cash Advance Introductory Period (Months)','CashAdvanceIntroductoryPeriod',4,'',true);
$addlArray[$k]->setData('Cash Advance Introductory Usage Fee (%)','CashAdvanceIntroductoryUsageFee',2,'',true);
$addlArray[$k]->setData('Cash Advance Introductory Minimum Fee ($)','CashAdvanceIntroductoryMinimumFee',4,'',true);
$addlArray[$k]->setData('Cash Advance Introductory Maximum Fee ($)','CashAdvanceIntroductoryMaximumFee',4,'',true);
$addlArray[$k]->setData('Cash Advance Introductory Fee Period (Months)','CashAdvanceIntroductoryFeePeriod',4,'',true);
$addlArray[$k]->setData('Cash Advance Introductory Fee Detail','CashAdvanceIntroductoryFeeDetail',5,'',false);
//Promotional Purchase Pricing
$addlArray[$k]->setData('Promotional Purchase Pricing');
$addlArray[$k]->setData('Promotional Purchase Pricing-Usage Offer (%)','PromotionalPurchasePricingUsageOfferPercent',2,'',true);
$addlArray[$k]->setData('Promotional Purchase Pricing Term-Usage Offer (Months)','PromotionalPurchasePricingUsageOfferMonth',4,'',true);
$k++;
$addlArray[$k] = new additionalDetails(179,'Credit Access Checks','cscan_credit_access_checks');
$addlArray[$k]->setData('Promotional Pricing Details');
$addlArray[$k]->setData('Promotional Offer','PromotionalOffer',3,'',true);
$addlArray[$k]->setData('Tier 1 Balance Transfer Introductory APR (%)','PromotionalOfferAPR',2,'',false);
$addlArray[$k]->setData('Tier 1 Balance Transfer Introductory Period (Months)','BalanceTransferIntroductoryPeriod_CAC1',4,'',false);
$addlArray[$k]->setData('Tier 2 Balance Transfer Introductory APR (%)','BalanceTransferIntroductoryAPR_CAC2',2,'',false);
$addlArray[$k]->setData('Tier 2 Balance Transfer Introductory Period (Months)','BalanceTransferIntroductoryPeriod_CAC2',4,'',false);
//start change rename and addded field
$addlArray[$k]->setData('Tier 1 Balance Transfer Introductory Usage Fee (%)','PromotionalOfferUsageFee',2,'',false);
$addlArray[$k]->setData('Tier 2 Balance Transfer Introductory Usage Fee (%)','Tier2BalanceTransferIntroductoryUsageFee',2,'',false);
$addlArray[$k]->setData('Tier 1 Balance Transfer Introductory Minimum Fee ($)','PromotionalOfferMinimumFee',4,'',false);
$addlArray[$k]->setData('Tier 2 Balance Transfer Introductory Minimum Fee ($)','Tier2BalanceTransferIntroductoryMinimumFee',4,'',false);
//$addlArray[$k]->setData('Balance Transfer Introductory Usage Fee (%)','PromotionalOfferUsageFee',2,'',false);
//$addlArray[$k]->setData('Balance Transfer Introductory Minimum Fee ($)','PromotionalOfferMinimumFee',4,'',false);
$addlArray[$k]->setData('Balance Transfer Introductory Maximum Fee ($)','PromotionalOfferMaximumFee',4,'',false);
$addlArray[$k]->setData('Balance Transfer Introductory Fee Period (Months)','BalanceTransferIntroductoryFeePeriod_CAC',4,'',false);
$addlArray[$k]->setData('Tier 1 Cash Advance Introductory APR (%)','CashAdvanceIntroductoryAPR_CAC',2,'',false);
$addlArray[$k]->setData('Tier 1 Cash Advance Introductory Period (Months)','CashAdvanceIntroductoryPeriod_CAC',4,'',false);
$addlArray[$k]->setData('Tier 2 Cash Advance Introductory APR (%)','Tier2CashAdvanceIntroductoryAPR_CAC',2,'',false);
$addlArray[$k]->setData('Tier 2 Cash Advance Introductory APR Period (Months)','Tier2CashAdvanceIntroductoryAPRPeriod_CAC',4,'',false);
$addlArray[$k]->setData('Cash Advance Introductory Usage Fee (%)','CashAdvanceIntroductoryUsageFee_CAC',2,'',false);
$addlArray[$k]->setData('Cash Advance Introductory Minimum Fee ($)','CashAdvanceIntroductoryMinimumFee_CAC',4,'',false);
$addlArray[$k]->setData('Cash Advance Introductory Maximum Fee ($)','CashAdvanceIntroductoryMaximumFee_CAC',4,'',false);
$addlArray[$k]->setData('Cash Advance Introductory Fee Period (Months)','CashAdvanceIntroductoryFeePeriod_CAC',4,'',false);
$addlArray[$k]->setData('Tier 1 Purchase Introductory APR (%)','PurchaseIntroductoryAPR_CAC',2,'',false);
$addlArray[$k]->setData('Tier 1 Purchase Introductory Period (Months)','PurchaseIntroductoryPeriod_CAC',4,'',false);
$addlArray[$k]->setData('Tier 2 Purchase Introductory APR (%)','Tier2PurchaseIntroductoryAPR_CAC',2,'',false);
$addlArray[$k]->setData('Tier 2 Purchase Introductory Period (Months)','Tier2PurchaseIntroductoryPeriod_CAC',4,'',false);
$addlArray[$k]->setData('Purchase Introductory Usage Fee (%)','PurchaseIntroductoryUsageFee_CAC',2,'',false);
$addlArray[$k]->setData('Purchase Introductory Minimum Fee ($)','PurchaseIntroductoryMinimumFee_CAC',4,'',false);
$addlArray[$k]->setData('Purchase Introductory Maximum Fee ($)','PurchaseIntroductoryMaximumFee_CAC',4,'',false);
$addlArray[$k]->setData('Purchase Introductory Fee Period (Months)','PurchaseIntroductoryFeePeriod_CAC',4,'',false);
$addlArray[$k]->setData('Regular Pricing');
$addlArray[$k]->setData('Balance Transfer Regular APR (%)','BalanceTransferRegularAPR_CAC',2,'',false);
$addlArray[$k]->setData('Balance Transfer Regular Rate Type','BalanceTransferRateType_CAC',1,'SELECT RateTypeName,RateTypeID FROM cscan_rate_type WHERE RateTypeID',false,'ORDER BY RateTypeSort'); 
$addlArray[$k]->setData('Balance Transfer Usage Fee (%)','BalanceTransferUsageFee_CAC',2,'',false);
$addlArray[$k]->setData('Balance Transfer Minimum Fee ($)','BalanceTransferMinimumFee_CAC',4,'',false); 
$addlArray[$k]->setData('Balance Transfer Maximum Fee ($)','BalanceTransferMaximumFee_CAC',4,'',false);
$addlArray[$k]->setData('Cash Advance Regular APR (%)','CashAdvanceRegularAPR_CAC',2,'',false);
$addlArray[$k]->setData('Cash Advance Regular Rate Type','CashAdvanceRateType_CAC',1,'SELECT RateTypeName,RateTypeID FROM cscan_rate_type WHERE RateTypeID',false,'ORDER BY RateTypeSort');
$addlArray[$k]->setData('Cash Advance Usage Fee (%)','CashAdvanceUsageFee_CAC',2,'',false); 
$addlArray[$k]->setData('Cash Advance Minimum Fee ($)','CashAdvanceMinimumFee_CAC',4,'',false);
$addlArray[$k]->setData('Cash Advance Maximum Fee ($)','CashAdvanceMaximumFee_CAC',4,'',false);
$addlArray[$k]->setData('Purchase Regular APR (%)','PurchaseRegularAPR_CAC',2,'',false);
$addlArray[$k]->setData('Purchase Regular Rate Type','PurchaseRateType_CAC',1,'SELECT RateTypeName,RateTypeID FROM cscan_rate_type WHERE RateTypeID',false,'ORDER BY RateTypeSort');
//$addlArray[$k]->setData('Balance Transfer Introductory APR Detail','BalanceTransferIntroductoryAPRDetail_CAC',5,'',false);
//$addlArray[$k]->setData('Balance Transfer Introductory Fee Detail','BalanceTransferIntroductoryFeeDetail_CAC',5,'',false);
//$addlArray[$k]->setData('Cash Advance Introductory Fee Detail','CashAdvanceIntroductoryFeeDetail_CAC',5,'',false);
//$addlArray[$k]->setData('Tier 2 Balance Transfer Regular APR (%)','Tier2BalanceTransferRegularAPR_CAC',2,'',false);
//$addlArray[$k]->setData('Tier 3 Balance Transfer Regular APR (%)','Tier3BalanceTransferRegularAPR_CAC',2,'',false);
//$addlArray[$k]->setData('Balance Transfer Regular APR (%) Detail','BalanceTransferRegularAPRDetail_CAC',5,'',false);
//$addlArray[$k]->setData('Tier 2 Cash Advance Regular APR (%)','Tier2CashAdvanceRegularAPR_CAC',2,'',false);
//$addlArray[$k]->setData('Tier 3 Cash Advance Regular APR (%)','Tier3CashAdvanceRegularAPR_CAC',2,'',false);
//$addlArray[$k]->setData('Cash Advance Regular APR (%) Detail','CashAdvanceRegularAPRDetail_CAC',5,'',false);
//$addlArray[$k]->setData('Tier 2 Purchase Regular APR (%)','Tier2PurchaseRegularAPR_CAC',2,'',false);
//$addlArray[$k]->setData('Tier 3 Purchase Regular APR (%)','Tier3PurchaseRegularAPR_CAC',2,'',false);
//$addlArray[$k]->setData('Purchase Regular APR (%) Detail','PurchaseRegularAPRDetail_CAC',5,'',false);
$k++;

$addlArray[$k] = new additionalDetails(87,'Banking','cscan_banking');
$addlArray[$k]->setData('General Banking Details');
$addlArray[$k]->setData('Minimum Deposit ($)','MinimumDeposit',4,'',true);
$addlArray[$k]->setData('Free Checking','FreeChecking',3,'',false);
//$addlArray[$k]->setData('Savings Interest Rate (%)','SavingsInterestRate',2,'',false);
$addlArray[$k]->setData('Checking APR (%)','Checking_APR',2,'',false);
$addlArray[$k]->setData('Checking APY (%)','Checking_APY',2,'',false);
$addlArray[$k]->setData('Savings APR (%)','Savings_APR',2,'',false);
$addlArray[$k]->setData('Savings APY (%)','Savings_APY',2,'',false);
$addlArray[$k]->setData('Money Market APR (%)','MoneyMarket_APR',2,'',false);
$addlArray[$k]->setData('Money Market APY (%)','MoneyMarket_APY',2,'',false);
$addlArray[$k]->setData('C/D APR (%)','CD_APR',2,'',false);
$addlArray[$k]->setData('C/D APY (%)','CD_APY',2,'',false);

$addlArray[$k]->setData('Debit Card Details');
$addlArray[$k]->setData('Debit Card Mentioned','DebitCardMentioned',3,'',true);
$addlArray[$k]->setData('Card Type','BankingCardType',1,'SELECT CardTypeName,CardTypeID FROM cscan_card_type WHERE CardTypeID',true,'ORDER BY CardTypeSort');
$addlArray[$k]->setData('Rewards Program Details');
$addlArray[$k]->setData('Rewards Program','BankingRewardsProgram',3,'',true);
$addlArray[$k]->setData('Rewards Program Emphasis','BankingRewardsProgramEmphasis',6,'SELECT RewardTypeName,RewardTypeID FROM cscan_reward_type WHERE RewardTypeID',false,'ORDER BY RewardTypeSort');
$k++;
$addlArray[$k] = new additionalDetails(6,'Mortgage & Loan','cscan_mortgage_loan');
$addlArray[$k]->setData('General Mortgage & Loan Details');
//if(!defined('ENV')){
//    define('ENV',getenv('SERVER_NAME'));
//}
//if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
    $addlArray[$k]->setData('Refinance','refinance',7,false,true);
    $addlArray[$k]->setData('Jumbo/Non-Conforming','jumbo_ncnfg',7,false,false);
    $addlArray[$k]->setData('VA','va',7,true,false);
    $addlArray[$k]->setData('FHA','fha',7,true,true);
    $addlArray[$k]->setData('Conventional','conventional',7,false);
    $addlArray[$k]->setData('USDA','usda',7);
    $addlArray[$k]->setData('Correspondent Lending','correspondent_lending',7,false,false);
//}
$addlArray[$k]->setData('Application Type','MLApplicationType',1,'SELECT ApplicationTypeName,ApplicationTypeID FROM cscan_application_type WHERE ApplicationTypeID',true,'ORDER BY ApplicationTypeSort');
//$addlArray[$k]->setData('Pre-Screen & Opt-Out Notice','MLOptOutFirmOffer',3,'',true);
$addlArray[$k]->setData('Regular Pricing Details');
$addlArray[$k]->setData('Offered Loan Amount ($)','OfferedLoanAmount',4,'',true);
$addlArray[$k]->setData('Maximum Loan Amount ($)','MaximumLoanAmount',4,'',true);
$addlArray[$k]->setData('Minimum Loan Amount ($)','MinimumLoanAmount',4,'',true);
$addlArray[$k]->setData('Loan Term (Months)','LoanTerm',4,'',true);
$addlArray[$k]->setData('Offered APR (%)','OfferedAPR',2,'',true);
$addlArray[$k]->setData('Upper APR (%)','UpperAPR',2,'',true);
$addlArray[$k]->setData('Lower APR (%)','LowerAPR',2,'',true);
$addlArray[$k]->setData('Rate Type','RateType',1,'SELECT RateTypeName,RateTypeID FROM cscan_rate_type WHERE RateTypeID',true,'ORDER BY RateTypeSort');
$addlArray[$k]->setData('Introductory Pricing Details');
$addlArray[$k]->setData('Introductory APR (%)','IntroductoryAPR',2,'',true);
$addlArray[$k]->setData('Introductory Period (Months)','IntroductoryPeriod',4,'',true);
$k++;
$addlArray[$k] = new additionalDetails(9,'Telecom','cscan_telecom');
$addlArray[$k]->setData('General Telecom Details');
$addlArray[$k]->setData('Plan Featured','FeaturedPlan',3,'',true);
$addlArray[$k]->setData('Featured Plan Name','FeaturedPlanName',0,'',false);
//$addlArray[$k]->setData('Plan Structure - TBD','PlanStructure',0,'',false);
$addlArray[$k]->setData('Contract Required','ContractRequired',3,'',true);
#################### for add new field internet speed #############################
 
$addlArray[$k]->setData('Internet Speed','internet_speed',0,'',true);  

#################### for add new field internet speed #############################
$addlArray[$k]->setData('Pricing');
$addlArray[$k]->setData('Monthly Cost ($)','MonthlyCost',4,'',true);
$addlArray[$k]->setData('Lower Featured Monthly Cost ($)','LowerFeaturedMonthlyCost',4,'',true);
$addlArray[$k]->setData('Upper Featured Monthly Cost ($)','UpperFeaturedMonthlyCost',4,'',true);
$addlArray[$k]->setData('Activation Charge ($)','ActivationCharge',4,'',true);
$addlArray[$k]->setData('Installation Charge ($)','InstallationCharge',4,'',false);
$addlArray[$k]->setData('Local Calling Monthly Cost ($)','LocalCallingMonthlyCost',4,'',false);
$addlArray[$k]->setData('Long Distance Monthly Cost ($)','LongDistanceMonthlyCost',4,'',false);
$addlArray[$k]->setData('Introductory Pricing Details');
$addlArray[$k]->setData('Introductory Cost ($)','TelecomIntroductoryCost',4,'',true);
$addlArray[$k]->setData('Introductory Period (Months)','TelecomIntroductoryPeriod',0,'',true);

$k++;
$addlArray[$k] = new additionalDetails(219,'Travel & Leisure','cscan_travel_leisure');
$addlArray[$k]->setData('Credit/Debit');
$addlArray[$k]->setData('Debit Card Mentioned','TLDebitCardMentioned',3,'',true);
$addlArray[$k]->setData('Credit Card Mentioned','TLCreditCardMentioned',3,'',true);
$addlArray[$k]->setData('Card Network','TLCardNetwork',6,'SELECT CardTypeName,CardTypeID FROM cscan_card_type WHERE CardTypeID',true,'ORDER BY CardTypeSort');
$k++;
/*$addlArray[$k] = new additionalDetails(266,'Retail','cscan_retail');
$addlArray[$k]->setData('Credit');
$addlArray[$k]->setData('Credit Card Mentioned','RCreditCardMentioned',3,'',true);
$k++;*/

$addlArray[$k] = new additionalDetails(315,'Energy','cscan_energy');
$addlArray[$k]->setData('Term Details');
$addlArray[$k]->setData('Rate Type','ERateType',1,'SELECT RateTypeName,RateTypeID FROM cscan_erate_type WHERE RateTypeID',true,'ORDER BY RateTypeSort');
$addlArray[$k]->setData('Offer Price (� per kWh)','EOfferPrice',4,'',true);
$addlArray[$k]->setData('Term Length','ETermLength',1,'SELECT TermLengthName,TermLengthID FROM cscan_eterm_length WHERE TermLengthID',false,'ORDER BY TermLengthSort');
$addlArray[$k]->setData('Cancel Fee','ECancelFee',3,'',true);
$addlArray[$k]->setData('Cancel Fee Detail','ECancelFeeDetail',5,'',false);
$addlArray[$k]->setData('Rate Type','ERateType2',1,'SELECT RateTypeName,RateTypeID FROM cscan_erate_type WHERE RateTypeID',false,'ORDER BY RateTypeSort');
$addlArray[$k]->setData('Offer Price (� per kWh)','EOfferPrice2',4,'',false);
$addlArray[$k]->setData('Term Length','ETermLength2',1,'SELECT TermLengthName,TermLengthID FROM cscan_eterm_length WHERE TermLengthID',false,'ORDER BY TermLengthSort');
$addlArray[$k]->setData('Rate Type','ERateType3',1,'SELECT RateTypeName,RateTypeID FROM cscan_erate_type WHERE RateTypeID',false,'ORDER BY RateTypeSort');
$addlArray[$k]->setData('Offer Price (� per kWh)','EOfferPrice3',4,'',false);
$addlArray[$k]->setData('Term Length','ETermLength3',1,'SELECT TermLengthName,TermLengthID FROM cscan_eterm_length WHERE TermLengthID',false,'ORDER BY TermLengthSort');
$addlArray[$k]->setData('Rate Type','ERateType4',1,'SELECT RateTypeName,RateTypeID FROM cscan_erate_type WHERE RateTypeID',false,'ORDER BY RateTypeSort');
$addlArray[$k]->setData('Offer Price (� per kWh)','EOfferPrice4',4,'',false);
$addlArray[$k]->setData('Term Length','ETermLength4',1,'SELECT TermLengthName,TermLengthID FROM cscan_eterm_length WHERE TermLengthID',false,'ORDER BY TermLengthSort');
$k++;
$addlArray[$k] = new additionalDetails(266,'Retail','cscan_retail');
$addlArray[$k]->setData('Credit');
$addlArray[$k]->setData('Credit Card Mentioned','RCreditCardMentioned',3,'',true);
$k++;
$GLOBALS['DRW'] = $DRW;
$GLOBALS['DRW_read'] = $DRW_read;
$GLOBALS['DRW_main'] = $DRW_main;
$GLOBALS['DRW_crm'] = $DRW_crm;

class additionalDetails {
	public $id;
	public $label;
	public $table;
	public $titles;
	public $fields;
	public $types;
	public $lookups;
	public $lookupos;
	public $displays;
	public $index;
	
	function __construct($id=0,$label='',$table='') {
		$this->id = $id;
		$this->label = $label;
		$this->table = $table;
		$this->titles = array();
		$this->fields = array();
		$this->types = array();
		$this->lookups = array();
		$this->lookupos = array();
		$this->displays = array();
		$this->index = -1;
	}
	function __destruct() {
		
	}
	public function setTitle($text){
		$this->titles[] = $text;
	}
	public function setField($text){
		$this->fields[] = $text;
	}
	public function setType($text){
		$this->types[] = $text;
	}
	public function setLookup($text){
		$this->lookups[] = $text;
	}
	public function setLookupo($text){
		$this->lookupos[] = $text;
	}
	public function setDisplay($text){
		$this->displays[] = $text;
	}
	public function setData($title,$field='',$type=0,$lookup='',$display=false,$lookupo=''){
		$this->setTitle($title);
		$this->setField($field);
		$this->setType($type);
		$this->setLookup($lookup);
		$this->setLookupo($lookupo);
		$this->setDisplay($display);
	}
	public function getTitle(){
		if(isset($this->titles[$this->index])){
			return $this->titles[$this->index];
		}
		return false;
	}
	public function getField(){
		if(isset($this->fields[$this->index])){
			return $this->fields[$this->index];
		}
		return false;
	}
	public function getType(){
		if(isset($this->types[$this->index])){
			return $this->types[$this->index];
		}
		return false;
	}
	public function getLookup(){
		if(isset($this->lookups[$this->index])){
			return $this->lookups[$this->index];
		}
		return false;
	}
	public function getLookupo(){
		if(isset($this->lookupos[$this->index])){
			return $this->lookupos[$this->index];
		}
		return false;
	}
	public function getDisplay(){
		if(isset($this->displays[$this->index])){
			return $this->displays[$this->index];
		}
		return false;
	}
	public function getNext(){
		$this->index++;
		if($this->getField()===false && $this->getTitle()===false){
			return false;
		}
		return true;
	}
	public function doReset(){
		$this->index = -1;
	}
	public function doPercent($input){
		return number_format($input*100,2);
	}
	public function doLookup($input,$mult=false){
		global $DRW,$DRW_read,$DRW_main,$DRW_crm;
		$sql = $this->getLookup();
		
		if($sql!==false && $sql!=''){
			if($mult){
				$input = trim($input);
				if(!empty($input)){
					$vals = array();
					$result = $DRW->query($sql.' IN ('.$input.')',$DRW_read);
					while($row = $DRW->fetch_row($result)){
						$vals[] = $row[0];
					}
					return implode(', ',$vals);
				}
			}
			else{
				$result = $DRW->query($sql.'='.$input,$DRW_read);
				$row = $DRW->fetch_row($result);
				return $row[0];
			}
		}
		else{
			return $input;
		}
	}
	public function getLookupQ(){
		$sql = $this->getLookup();
		$sql = preg_replace('/\\s+where.+$/i','',$sql);
		$sqlo = $this->getLookupo();
		
		return $sql.' '.$sqlo;
	}
	public function doProcess($input){
		if(!is_null($input)){
			$type = $this->getType();
			switch($type){
				//0 = text varchar(255) NOT NULL default ''
				//5 = text text
				case 1://lookup int(10) unsigned NOT NULL default '0'
					return $this->doLookup($input);
				case 2://percent decimal(5,4) default NULL
					return $this->doPercent($input);
				case 3://bool tinyint(1) unsigned NOT NULL default '0'
					if($input){
						return 'Yes';
					}
					else{
						if($this->getDisplay()){
							return 'No';
						}
						return '';
					}
				case 4: // number decimal(12,2) default NULL or int(4) unsigned default NULL
					if(strpos($input,'.')!==false){
						return number_format($input,2);
					}
					return number_format($input);
				case 6://lookup mult varchar(200) not null default ''
					return $this->doLookup($input,true);
                                    
                                case 7://bool tinyint(1) unsigned NOT NULL default '0'
					if($input){
						return 'Yes';
					}
					else{
						if($this->getDisplay()){
							return 'No';
						}
						return '';
					}
			}
		}
		return $input;
	}
	public function doPrepare($input=''){
		$type = $this->getType();
		switch($type){
			//0, 5 = text
			case 1://lookup
			case 3://bool
				return (int)$input;
			case 2://percent
				$input = trim($input);
				if($input!=''){
					return ((float)preg_replace('/[^0-9\\.]/','',$input))/100;
				}
				else{
					return '';
				}
			case 4:
				$input = trim($input);
				if($input!=''){
					return (float)preg_replace('/[^0-9\\.]/','',$input);
				}
				else{
					return '';
				}
			case 6://lookup mult
				if(is_array($input)){
					$input = implode(',',$input);
				}
				else{
					return '';
				}
                        case 7://bool
				return (int)$input;
		}
		return $input;
	}
	public function doFormHTML($field='',$value='',$extra=''){
		global $DRW,$DRW_read,$DRW_main,$DRW_crm;
		$out = '';
		$type = $this->getType();
		switch($type){
			case 1://lookup
			case 6://lookup mult
				if($type==6){
					$extra .= ' size="3" multiple="multiple"';
					if($value!='') {
						$value = explode(',',$value);
					}
					else {
						$value = array();
					}
				}
				else{
					$extra .= ' size="1"';
				}
				$sfield = preg_replace('/[^a-zA-Z0-9_-]+/','',$field);
				$out .= '<select name="'.$field.'" id="'.$sfield.'" class="combo_box"'.$extra.'>';
				if($type==1){
					$out .= '<option value="0">-- Select One --</option>';
				}
				$query_s = $this->getLookupQ();
				$result_s = $DRW->query($query_s,$DRW_read);
				while($row_s = $DRW->fetch_row($result_s)){
					$id = $row_s[1];
					$name = $row_s[0];
					$out .= "<option value=\"$id\"";
					if(($type==1 && $id==$value) || ($type==6 && in_array($id,$value))) {
						$out .= " selected=\"selected\"";
					}
					$out .= ">".htmlspecialchars($name)."</option>";
				}
				$out .= '</select>';
				break;
			case 3://bool
				$out .= '<label><input type="checkbox" id="'.$field.'" name="'.$field.'" value="1"';
				if($value==1) {
					$out .= ' checked="checked"';
				}
				$out .= $extra.' />Yes</label>';
				break;
                        case 7://bool
				$out .= '<input type="checkbox" id="'.$field.'" name="'.$field.'" value="1"';
				if($value==1) {
					$out .= ' checked="checked"';
				}
				$out .= $extra.' />';
				break;
			case 5: //multi text
				$out .= '<textarea rows="5" cols="40" name="'.$field.'" id="'.$field.'" class="input_box"'.$extra.'>'.htmlspecialchars($value,ENT_QUOTES).'</textarea>';
				break;
			default: //text
				if($type==2){ //percent
					$extra .= ' size="5" maxlength="6"';
					if($value!=''){
						$value*=100;
					}
				}
				elseif($type==4){ //int
					$extra .= ' size="5" maxlength="15"';
				}
				else{
					$extra .= ' size="40" maxlength="255"';
				}
				$out .= '<input type="text" name="'.$field.'" id="'.$field.'" class="input_box" value="'.htmlspecialchars($value,ENT_QUOTES).'"'.$extra.' />';
		}
		return $out;
	}
}
?>