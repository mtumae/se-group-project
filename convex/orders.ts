import { paginationOptsValidator } from "convex/server";
import { mutation, query } from "./_generated/server";
import { v } from "convex/values";



export const getOrders = query({
    args:{
        paginationOpts: paginationOptsValidator
    },
    handler: async (ctx, args) => {
        const data = await ctx.db
        .query("orders")
        .order("desc")
        .paginate(args.paginationOpts)
        return data
    }
});

export const deleteOrder = mutation({   
    args: {
        orderId: v.id("orders")
    },
    handler: async (ctx, args) => {
        await ctx.db.delete(args.orderId);
    }
});

export const getOrdersByUserId = query({
    args:{
        userId: v.string()
    },
    handler: async (ctx, args) => {
        const orders = await ctx.db
        .query("orders")
        .withIndex("byUserId", (q) => q.eq("userId", args.userId))
        .order("desc")
        .collect()
        return orders
    }
});



export const addOrder = mutation({
    args: {
        items: v.array(
            v.object({
                userId: v.string(),
                username:v.string(),
                itemId: v.id("items"),
                itemName: v.string(),
            })
        )
    },
    handler: async (ctx, args) => {
        const {items} = args
        for (const item of items){
            await ctx.db.insert("orders", {
                userId: item.userId,
                username:item.username,
                itemId: item.itemId,
                itemName: item.itemName,
                orderDate: new Date().toISOString(),
                status: "pending",
            });
            await ctx.db.delete(item.itemId)
        }
        
    }
});



export const getAllOrders = query({
    handler: async (ctx) => {
        const orders = await ctx.db.query("orders").collect();
        return orders;
    }
});


export const updateOrderStatus = mutation({
    args: {
        orderId: v.id("orders"),
        status: v.string()
    },
    handler: async (ctx, args) => {
        await ctx.db.patch(args.orderId, {
            status: args.status}
        )
    }
});



export const getOrdersForUserId = query({
    args:{
        userId: v.string()
    },
    handler: async (ctx, args) => {
        const orders = await ctx.db
        .query("orders")
        .withIndex("byUserId", (q) => q.eq("userId", args.userId))
        .order("desc")
        .collect()
        return orders
    }
});