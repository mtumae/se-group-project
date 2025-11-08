import { paginationOptsValidator } from "convex/server";
import { mutation, query } from "./_generated/server";
import { convexToJson, v } from "convex/values";
import { faker } from "@faker-js/faker";

export const generateUploadUrl = mutation({
  handler: async (ctx) => {
    return await ctx.storage.generateUploadUrl();
  },
});

export const addItem = mutation({
  args: { 
    userId: v.string(),
    username: v.string(),
    imageUrl: v.id("_storage"),
    itemName: v.string(),
    itemDescription: v.string(),
    price: v.number(),
    category: v.string(),
    createdAt: v.string(),

},
  handler: async (ctx, args) => {
    await ctx.db.insert("items", {
      userId: args.userId,
      username: args.username,
      imageUrl: args.imageUrl,
      itemName: args.itemName,
      itemDescription: args.itemDescription,
      price: args.price,
      category: args.category,
      createdAt: args.createdAt,
    });
  },
});


export const getAllItems = query({
    handler: async (ctx) => {
        const items = await ctx.db
        .query("items")
        .order("desc")
        .take(50)

       return Promise.all(
        items.map(async (item) => ({
          ...item,
          // If the item is an "image" its `body` is an `Id<"_storage">`
          url: await ctx.storage.getUrl(item.imageUrl!), 
          }))
      )
    }
});



export const getByCategory = query({
  args:{
    category: v.string(),
  },
  handler: async (ctx, args) => {
    return await ctx.db
      .query("items")
      .filter((q) => q.eq("category", args.category))
      .collect();
  }
});

export const search = query({
  args:{
    query: v.string(),
    category:v.string(),
  },
  handler: async (ctx, args) => {
    console.log("ARGS: ",args)
    if(args.category){
      console.log("Found category ONLY, searching for :", args.category);
      return  await ctx.db
      .query("items")
      .filter((q) => q.eq("category", args.category))
      .collect()
 
  }else if(args.query){
    console.log("Found query ONLY, searching for :", args.query);
    return  await ctx.db
    .query("items")
    .withSearchIndex("itemName", 
    (q)=>q.search("itemName",args.query)
    )
    .take(10);
  }
  else if(args.query && args.category){
    console.log("Found query AND category, searching for :", args.query, " in category: ", args.category);
    return  await ctx.db
    .query("items")
    .withSearchIndex("itemName", 
    (q)=>q.search("itemName",args.query).eq("category", args.category)
    )
    .take(10);

  }else{
    console.log("No search query or category provided.");
    return
  }
    }
});

function createRandomItem(){
    return {
            id: faker.string.uuid(),
            name: faker.commerce.productName(),
            description: faker.commerce.productDescription(),
            price: Number.parseFloat(faker.commerce.price()),
            orders: faker.number.int({ min: 0, max: 1000 }),
            category: faker.commerce.department(),
            imageUrl: faker.image.url({ width: 640, height: 480}),
            userid: faker.person.firstName()+faker.string.uuid(),
            username: faker.person.fullName(),
        };
}

export const getRandomItems = query({
    handler: async (ctx) => {
        faker.seed()
        const data = Array.from({length:20}, () => null).map(() => 
          createRandomItem()
      )
        console.log(data.length, " items generated")

        
        return data
    }
})



export const addRandomItems = mutation({
    handler: async (ctx) => {
        faker.seed()
        const data = Array.from({length:50}, () => null).map(() => 
          createRandomItem()
      )
        console.log(data.length, " items generated")

        for (const item of data) {
            await ctx.db.insert("items", {
                userId: item.userid,
                username: item.username,
                link: item.imageUrl,
                itemName: item.name,
                itemDescription: item.description,
                price: item.price,
                category: item.category,
                createdAt: new Date().toISOString(),
            });
        }
    }
})






export const priceRangesearch = query({
  args:{
    minPrice: v.number(),
    maxPrice: v.number(),
  },
  handler: async (ctx, args) => {
    const unsorted =  await ctx.db.query("items").collect()
    const sorted = unsorted.filter((item)=>{
    const price = Number(item.price)
      return price >= args.minPrice && price <= args.maxPrice
    })


    console.log("Items in price range:", sorted.length);
    return sorted;
  }
});

